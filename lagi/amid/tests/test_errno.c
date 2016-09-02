
#include <errno.h>
#include <stdio.h>
#include <string.h>

int main(int argc, char * argv[])
{
	int i = 0;

	for ( i = 0; i < 256; i++ ) {
		printf("errno: %03d\t/* %s */\n", i, strerror(i));
	}

	return 0;
}
