#pragma once

#include "../dependencies/imgui/imgui.h"
#include "../dependencies/imgui/backend/imgui_impl_win32.h"
#include "../dependencies/imgui/backend/imgui_impl_dx9.h"
#include <d3d9.h>
#include <dinput.h>
#include <tchar.h>

#pragma comment (lib,"d3d9.lib")

#define DIRECTINPUT_VERSION 0x0800

extern LPDIRECT3D9           g_d3d;
extern LPDIRECT3DDEVICE9     g_d3d_device;
extern D3DPRESENT_PARAMETERS g_d3d_params;
extern IMGUI_IMPL_API LRESULT ImGui_ImplWin32_WndProcHandler(HWND, UINT, WPARAM, LPARAM);

namespace gui {
	bool create_device(HWND);
	void cleanup_device();
	void reset_device();
	LRESULT WINAPI wnd_proc(HWND, UINT, WPARAM, LPARAM);
	void padded_text(const char*, float, float);
	void init();
}