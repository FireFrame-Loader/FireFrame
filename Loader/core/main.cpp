#include "../gui/gui.h"
#include "../security/lazy_importer.h"

int main() {
	LI_FN(FreeConsole)();
	gui::init();

	return EXIT_SUCCESS;
}