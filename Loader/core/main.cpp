#include "../gui/gui.h"
#include "../dependencies/security/lazy_importer.h"

int main() {
	LI_FN(FreeConsole)();
	gui::init();

	return EXIT_SUCCESS;
}