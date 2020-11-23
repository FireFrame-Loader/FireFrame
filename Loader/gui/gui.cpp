#include "time.h"
#include "gui.h"
#include "background.h"
#include "font.h"
#include "../dependencies/security/skcrypt.h"
#include "../dependencies/security/lazy_importer.h"
#include "../dependencies/imgui/imgui_internal.h"
#include <D3dx9tex.h>
#include <chrono>

#pragma comment(lib, "D3dx9")

static LPDIRECT3D9           g_d3d		  = nullptr;
static LPDIRECT3DDEVICE9     g_d3d_device = nullptr;
static D3DPRESENT_PARAMETERS g_d3d_params = {};

// TODO: move these to their own namespace variables or something
char register_username[256];
char register_password[256];
char register_confirm_pw[256];
char username[256];
char password[256];
char license_code[20];
int selected_module = 0;
const char* const modules_list[] = { "CS:GO", "PUBG", "Rust" };

void gui::init() {
    static int screen_width = LI_FN(GetSystemMetrics)(SM_CXSCREEN);
    static int screen_height = LI_FN(GetSystemMetrics)(SM_CYSCREEN);
    static auto window_size = ImVec2(626.f, 363.f);
    static PDIRECT3DTEXTURE9 my_texture = nullptr;
    static bool* window_open = nullptr;
    static int current_window = 0;
    static int current_tab = 0;

    static auto window_name_wchar = skCrypt(L"FireFrame");

    // Create application window
    //ImGui_ImplWin32_EnableDpiAwareness();
    WNDCLASSEX wc = { sizeof(WNDCLASSEX), CS_CLASSDC, wnd_proc, 0L, 0L, GetModuleHandle(NULL), NULL, NULL, NULL, NULL, window_name_wchar, NULL };

    LI_FN(RegisterClassExW)(&wc);

    window_name_wchar.encrypt();

    HWND hwnd = ::CreateWindow(wc.lpszClassName, window_name_wchar, (WS_POPUP | WS_SYSMENU | WS_MINIMIZEBOX | WS_VISIBLE | WS_CAPTION), screen_width / 2 - 320, screen_height / 2 - 196, 640, 400, NULL, NULL, wc.hInstance, NULL);

    window_name_wchar.clear();

    // Initialize Direct3D
    if (!create_device(hwnd)) {
        cleanup_device();
        LI_FN(UnregisterClassW)(wc.lpszClassName, wc.hInstance);
        return;
    }

    // Show the window
    LI_FN(ShowWindow)(hwnd, SW_SHOWDEFAULT);
    LI_FN(UpdateWindow)(hwnd);

    // Setup Dear ImGui context
    IMGUI_CHECKVERSION();
    ImGui::CreateContext();
    ImGuiIO& io = ImGui::GetIO(); (void)io;
    //io.ConfigFlags |= ImGuiConfigFlags_NavEnableKeyboard;     // Enable Keyboard Controls
    //io.ConfigFlags |= ImGuiConfigFlags_NavEnableGamepad;      // Enable Gamepad Controls

    // Setup Dear ImGui style
    ImGui::StyleColorsDark();
    //ImGui::StyleColorsClassic();

    ImGuiStyle* style = &ImGui::GetStyle();
    style->WindowRounding = 0.0f;
    style->FrameRounding = 3.0f;
    style->ChildRounding = 3.0f;
    style->GrabRounding = 3.0f;
    style->PopupRounding = 3.0f;
    style->ScrollbarRounding = 3.0f;
    style->TabRounding = 3.0f;
    style->Colors[ImGuiCol_Button] = ImVec4(0.10f, 0.11f, 0.12f, 1.0f);
    style->Colors[ImGuiCol_ButtonHovered] = ImVec4(0.14f, 0.16f, 0.16f, 1.0f);
    style->Colors[ImGuiCol_ButtonActive] = ImVec4(0.16f, 0.17f, 0.18f, 1.0f);
    style->Colors[ImGuiCol_Tab] = ImVec4(0.10f, 0.11f, 0.12f, 1.0f);
    style->Colors[ImGuiCol_TabHovered] = ImVec4(0.14f, 0.16f, 0.16f, 1.0f);
    style->Colors[ImGuiCol_TabActive] = ImVec4(0.16f, 0.17f, 0.18f, 1.0f);
    style->Colors[ImGuiCol_FrameBg] = ImVec4(0.09f, 0.10f, 0.10f, 1.0f);
    style->Colors[ImGuiCol_FrameBgHovered] = ImVec4(0.09f, 0.10f, 0.10f, 1.0f);
    style->Colors[ImGuiCol_FrameBgActive] = ImVec4(0.09f, 0.10f, 0.10f, 1.0f);
    style->Colors[ImGuiCol_Header] = ImVec4(0.22f, 0.23f, 0.24f, 1.0f);
    style->Colors[ImGuiCol_HeaderHovered] = ImVec4(0.24f, 0.25f, 0.26f, 1.0f);
    style->Colors[ImGuiCol_HeaderActive] = ImVec4(0.26f, 0.27f, 0.28f, 1.0f);
    style->Colors[ImGuiCol_ChildBg] = ImVec4(0.07f, 0.08f, 0.06f, 1.0f);

    // Setup Platform/Renderer backends
    ImGui_ImplWin32_Init(hwnd);
    ImGui_ImplDX9_Init(g_d3d_device);

    // Load Fonts
    // - If no fonts are loaded, dear imgui will use the default font. You can also load multiple fonts and use ImGui::PushFont()/PopFont() to select them.
    // - AddFontFromFileTTF() will return the ImFont* so you can store it if you need to select the font among multiple.
    // - If the file cannot be loaded, the function will return NULL. Please handle those errors in your application (e.g. use an assertion, or display an error and quit).
    // - The fonts will be rasterized at a given size (w/ oversampling) and stored into a texture when calling ImFontAtlas::Build()/GetTexDataAsXXXX(), which ImGui_ImplXXXX_NewFrame below will call.
    // - Read 'docs/FONTS.md' for more instructions and details.
    // - Remember that in C/C++ if you want to include a backslash \ in a string literal you need to write a double backslash \\ !
    //io.Fonts->AddFontDefault();
    //io.Fonts->AddFontFromFileTTF("../../misc/fonts/Roboto-Medium.ttf", 16.0f);
    //io.Fonts->AddFontFromFileTTF("../../misc/fonts/Cousine-Regular.ttf", 15.0f);
    //io.Fonts->AddFontFromFileTTF("../../misc/fonts/DroidSans.ttf", 16.0f);
    //io.Fonts->AddFontFromFileTTF("../../misc/fonts/ProggyTiny.ttf", 10.0f);
    //ImFont* font = io.Fonts->AddFontFromFileTTF("c:\\Windows\\Fonts\\ArialUni.ttf", 18.0f, NULL, io.Fonts->GetGlyphRangesJapanese());
    //IM_ASSERT(font != NULL);

    io.IniFilename = nullptr;

    ImFont* segoe_ui = io.Fonts->AddFontFromMemoryCompressedTTF(raw_data_font, raw_data_font_size, 24.0f);
    IM_ASSERT(segoe_ui != NULL);

    ImFont* segoe_ui_big = io.Fonts->AddFontFromMemoryCompressedTTF(raw_data_font, raw_data_font_size, 48.0f);
    IM_ASSERT(segoe_ui_big != NULL);

    D3DXCreateTextureFromFileInMemory(g_d3d_device, raw_data_image, sizeof(raw_data_image), &my_texture);

    // Our state
    ImVec4 clear_color = ImVec4(0.45f, 0.55f, 0.60f, 1.00f);

    // Main loop
    MSG msg;
    //RtlZeroMemory(&msg, sizeof(msg)); //memset((Destination),0,(Length))
    LI_FN(memset)(&msg, 0, sizeof(msg));

    static auto window_name = skCrypt("FireFrame");
    static auto header = skCrypt("FireFrame - Loader");
    static auto username_login = skCrypt("Username");
    static auto password_login = skCrypt("Password");
    static auto username_login_hidden = skCrypt("##username");
    static auto password_login_hidden = skCrypt("##password");
    static auto login = skCrypt("Login");
    static auto module_selection = skCrypt("Module Selection");
    static auto module_tabs = skCrypt("##moduletabs");
    static auto modules = skCrypt("Modules");
    static auto modules_hidden = skCrypt("##modules");
    static auto load = skCrypt("Load");
    static auto redeem_tab = skCrypt("Redeem");
    static auto license = skCrypt("##license");
    static auto redeem = skCrypt("Redeem");
    static auto key = skCrypt("Key");
    static auto register_ = skCrypt("Register");
    static auto username_register = skCrypt("##registerusername");
    static auto password_register = skCrypt("##registerpw");
    static auto confirm_pw_register = skCrypt("##confirmpw");
    static auto confirm_password = skCrypt("Confirm Password");
    static auto remaining_time = skCrypt("Expires On: %i-%i-%i");

    time_t time = 32879409516;
    auto converted_time = std::chrono::system_clock::from_time_t(time);
    auto final_date = date::year_month_day(floor<date::days>(converted_time));

    while (msg.message != WM_QUIT) {
        // Poll and handle messages (inputs, window resize, etc.)
        // You can read the io.WantCaptureMouse, io.WantCaptureKeyboard flags to tell if dear imgui wants to use your inputs.
        // - When io.WantCaptureMouse is true, do not dispatch mouse input data to your main application.
        // - When io.WantCaptureKeyboard is true, do not dispatch keyboard input data to your main application.
        // Generally you may always pass all inputs to dear imgui, and hide them from your application based on those two flags.
        if (PeekMessageW(&msg, NULL, 0U, 0U, PM_REMOVE)) {
            TranslateMessage(&msg);
            DispatchMessageW(&msg);
            continue;
        }

        // Start the Dear ImGui frame
        ImGui_ImplDX9_NewFrame();
        ImGui_ImplWin32_NewFrame();
        ImGui::NewFrame();

        ImGui::SetNextWindowPos(ImVec2(-1.f, -1.f));
        ImGui::SetNextWindowSize(window_size);

        ImGui::Begin(window_name, window_open, ImGuiWindowFlags_NoCollapse | ImGuiWindowFlags_NoResize | ImGuiWindowFlags_NoMove | ImGuiWindowFlags_NoTitleBar | ImGuiWindowFlags_NoSavedSettings);

        ImGui::GetCurrentWindow()->DrawList->AddImage((void*)my_texture, ImVec2(0.f, 0.f), ImVec2(626.f, 363.f));

        switch (current_window) {
        case 0:
            ImGui::PushFont(segoe_ui_big);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + (window_size.x / 4) - 2.5f);

            ImGui::Text(header);

            ImGui::PopFont();

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + window_size.x / 2 - 47.f);

            ImGui::Text(username_login);

            username_login.encrypt();

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            ImGui::SetNextItemWidth(407.f);

            ImGui::InputText(username_login_hidden, username, sizeof(username));

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + window_size.x / 2 - 44.f);

            ImGui::Text(password_login);

            password_login.encrypt();

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            ImGui::SetNextItemWidth(407.f);

            ImGui::InputText(password_login_hidden, password, sizeof(password), ImGuiInputTextFlags_Password);

            ImGui::SetCursorPos(ImVec2(ImGui::GetCursorPos().x + 94.5f, ImGui::GetCursorPos().y + 55.f));

            if (ImGui::Button(login, ImVec2(408.f, 30.f)))
                current_window = 1;

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            if (ImGui::Button(register_, ImVec2(408.f, 30.f))) {
                register_.encrypt();
                current_window = 2;
            }
            break;
        case 1:
            ImGui::PushFont(segoe_ui_big);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + (window_size.x / 4) + 11.f);

            ImGui::Text(module_selection);

            ImGui::PopFont();

            ImGui::SetCursorPosX(230.f);

            if (ImGui::Button(modules, ImVec2(80, 35)))
                current_tab = 0;

            ImGui::SameLine();

            if (ImGui::Button(redeem_tab, ImVec2(80, 35)))
                current_tab = 1;

            switch (current_tab) {
            case 0:
                ImGui::BeginChild(1, ImVec2(301.f, 216.f), true);

                ImGui::SetNextItemWidth(285.f);

                ImGui::ListBox(modules_hidden, &selected_module, modules_list, IM_ARRAYSIZE(modules_list));

                ImGui::EndChild();

                ImGui::SameLine();

                ImGui::BeginChild(2, ImVec2(301.f, 216.f), true);

                ImGui::Text(remaining_time, final_date.day(), final_date.month(), final_date.year());

                ImGui::EndChild();

                ImGui::Button(load, ImVec2(610.f, 36.f));
                break;
            case 1:
                ImGui::BeginChild(3, ImVec2(610.f, 256.f), true);

                ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 282.5f);

                ImGui::Text(key);

                ImGui::SetNextItemWidth(594.f);

                ImGui::InputText(license, license_code, sizeof(license_code));

                ImGui::SetCursorPosY(ImGui::GetCursorPos().y + 141.f);

                ImGui::Button(redeem, ImVec2(594.f, 37.f));

                ImGui::EndChild();
                break;
            }
            break;
        case 2:
            ImGui::PushFont(segoe_ui_big);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + (window_size.x / 2) - 74.5f);

            ImGui::Text(register_);

            register_.encrypt();

            ImGui::PopFont();

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + window_size.x / 2 - 49.f);

            ImGui::Text(username_login);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            ImGui::SetNextItemWidth(407.f);

            ImGui::InputText(username_register, register_username, sizeof(register_username));

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + window_size.x / 2 - 45.f);

            ImGui::Text(password_login);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            ImGui::SetNextItemWidth(407.f);

            ImGui::InputText(password_register, register_password, sizeof(register_password), ImGuiInputTextFlags_Password);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + window_size.x / 2 - 79.f);

            ImGui::Text(confirm_password);

            ImGui::SetCursorPosX(ImGui::GetCursorPos().x + 94.5f);

            ImGui::SetNextItemWidth(407.f);

            ImGui::InputText(confirm_pw_register, register_confirm_pw, sizeof(register_confirm_pw), ImGuiInputTextFlags_Password);

            ImGui::SetCursorPos(ImVec2(ImGui::GetCursorPos().x + 94.5f, ImGui::GetCursorPos().y + 41.f));

            if (ImGui::Button(register_, ImVec2(408.f, 30.f)))
                current_window = 1;

            break;
        }

        ImGui::End();

        // Rendering
        ImGui::EndFrame();
        g_d3d_device->SetRenderState(D3DRS_ZENABLE, FALSE);
        g_d3d_device->SetRenderState(D3DRS_ALPHABLENDENABLE, FALSE);
        g_d3d_device->SetRenderState(D3DRS_SCISSORTESTENABLE, FALSE);
        D3DCOLOR clear_col_dx = D3DCOLOR_RGBA((int)(clear_color.x * 255.0f), (int)(clear_color.y * 255.0f), (int)(clear_color.z * 255.0f), (int)(clear_color.w * 255.0f));
        g_d3d_device->Clear(0, NULL, D3DCLEAR_TARGET | D3DCLEAR_ZBUFFER, clear_col_dx, 1.0f, 0);

        if (g_d3d_device->BeginScene() >= 0) {
            ImGui::Render();
            ImGui_ImplDX9_RenderDrawData(ImGui::GetDrawData());
            g_d3d_device->EndScene();
        }

        HRESULT result = g_d3d_device->Present(NULL, NULL, NULL, NULL);

        // Handle loss of D3D9 device
        if (result == D3DERR_DEVICELOST && g_d3d_device->TestCooperativeLevel() == D3DERR_DEVICENOTRESET)
            reset_device();
    }

    window_name.clear();
    header.clear();
    username_login.clear();
    password_login.clear();
    username_login_hidden.clear();
    password_login_hidden.clear();
    login.clear();
    module_selection.clear();
    module_tabs.clear();
    modules.clear();
    modules_hidden.clear();
    load.clear();
    redeem_tab.clear();
    license.clear();
    redeem.clear();
    key.clear();
    register_.clear();
    username_register.clear();
    password_register.clear();
    confirm_pw_register.clear();
    confirm_password.clear();
    remaining_time.clear();

    ImGui_ImplDX9_Shutdown();
    ImGui_ImplWin32_Shutdown();
    ImGui::DestroyContext();

    cleanup_device();
    LI_FN(DestroyWindow)(hwnd);
    LI_FN(UnregisterClassW)(wc.lpszClassName, wc.hInstance);
}

bool gui::create_device(HWND hwnd) {
    if ((g_d3d = Direct3DCreate9(D3D_SDK_VERSION)) == NULL)
        return false;

    // Create the D3DDevice
    //ZeroMemory(&g_d3d_params, sizeof(g_d3d_params)); //memset((Destination),0,(Length))
    LI_FN(memset)(&g_d3d_params, 0, sizeof(g_d3d_params));
    g_d3d_params.Windowed = TRUE;
    g_d3d_params.SwapEffect = D3DSWAPEFFECT_DISCARD;
    g_d3d_params.BackBufferFormat = D3DFMT_UNKNOWN;
    g_d3d_params.EnableAutoDepthStencil = TRUE;
    g_d3d_params.AutoDepthStencilFormat = D3DFMT_D16;
    g_d3d_params.PresentationInterval = D3DPRESENT_INTERVAL_ONE;           // Present with vsync
    //g_d3dpp.PresentationInterval = D3DPRESENT_INTERVAL_IMMEDIATE;   // Present without vsync, maximum unthrottled framerate

    if (g_d3d->CreateDevice(D3DADAPTER_DEFAULT, D3DDEVTYPE_HAL, hwnd, D3DCREATE_HARDWARE_VERTEXPROCESSING, &g_d3d_params, &g_d3d_device) < 0)
        return false;

    return true;
}

void gui::cleanup_device() {
    if (g_d3d_device) { g_d3d_device->Release(); g_d3d_device = NULL; }
    if (g_d3d) { g_d3d->Release(); g_d3d = NULL; }
}

void gui::reset_device() {
    ImGui_ImplDX9_InvalidateDeviceObjects();
    HRESULT hr = g_d3d_device->Reset(&g_d3d_params);

    if (hr == D3DERR_INVALIDCALL)
        IM_ASSERT(0);

    ImGui_ImplDX9_CreateDeviceObjects();
}

LRESULT WINAPI gui::wnd_proc(HWND hwnd, UINT msg, WPARAM wparam, LPARAM lparam) {
    if (ImGui_ImplWin32_WndProcHandler(hwnd, msg, wparam, lparam))
        return true;

    switch (msg) {
    case WM_SIZE:
        if (g_d3d_device != NULL && wparam != SIZE_MINIMIZED) {
            g_d3d_params.BackBufferWidth = LOWORD(lparam);
            g_d3d_params.BackBufferHeight = HIWORD(lparam);
            reset_device();
        }
        return 0;
    case WM_SYSCOMMAND:
        if ((wparam & 0xfff0) == SC_KEYMENU) // Disable ALT application menu
            return 0;
        break;
    case WM_DESTROY:
        LI_FN(PostQuitMessage)(0);
        return 0;
    }

    return DefWindowProcW(hwnd, msg, wparam, lparam);
}