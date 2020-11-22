#include "gui.h"
#include "../dependencies/security/skcrypt.h"
#include "../dependencies/security/lazy_importer.h"

static LPDIRECT3D9           g_d3d		  = nullptr;
static LPDIRECT3DDEVICE9     g_d3d_device = nullptr;
static D3DPRESENT_PARAMETERS g_d3d_params = {};

void gui::init() {
    static int screen_width = LI_FN(GetSystemMetrics)(SM_CXSCREEN);
    static int screen_height = LI_FN(GetSystemMetrics)(SM_CYSCREEN);
    static auto window_name_wchar = skCrypt(L"FireFrame");
    static auto window_name = skCrypt("FireFrame");
    static auto window_size = ImVec2(1266.f, 762.f);
    static bool* window_open = nullptr;

    // Create application window
    //ImGui_ImplWin32_EnableDpiAwareness();
    WNDCLASSEX wc = { sizeof(WNDCLASSEX), CS_CLASSDC, wnd_proc, 0L, 0L, GetModuleHandle(NULL), NULL, NULL, NULL, NULL, window_name_wchar, NULL };

    LI_FN(RegisterClassExW)(&wc);

    window_name_wchar.encrypt();

    HWND hwnd = ::CreateWindow(wc.lpszClassName, window_name_wchar, (WS_POPUP | WS_SYSMENU | WS_MINIMIZEBOX | WS_VISIBLE | WS_CAPTION), screen_width / 2 - 640, screen_height / 2 - 400, 1280, 800, NULL, NULL, wc.hInstance, NULL);

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

    // Our state
    ImVec4 clear_color = ImVec4(0.45f, 0.55f, 0.60f, 1.00f);

    // Main loop
    MSG msg;
    //RtlZeroMemory(&msg, sizeof(msg)); //memset((Destination),0,(Length))
    memset(&msg, 0, sizeof(msg));

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

        ImGui::SetNextWindowPos(ImVec2(-1.f, 0.f));
        ImGui::SetNextWindowSize(window_size);

        ImGui::Begin(window_name, window_open, ImGuiWindowFlags_NoCollapse | ImGuiWindowFlags_NoResize | ImGuiWindowFlags_NoMove);

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
    memset(&g_d3d_params, 0, sizeof(g_d3d_params));
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