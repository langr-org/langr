
#include <errno.h>
#include <stdio.h>
#include <string.h>

int main(int argc, char * argv[])
{
	int i = 0;
	int a[] = {0,1,2,3,4,5,6};
	int b[5*2] = {9};
	char c[20];
	char * d[20];
	char ** g;
	short int e[10<<1];
	short int f[1+10];

	printf("int a[]:s%d, \tint b[5*2]:s%d,\nchar c[20]:s%d,l%d \tchar * d[20]:s%d,l%d\nchar **g:s%d\nshort e[10<<1]:s%d, \tshort f[1+10]:s%d,\n", 
			sizeof(a), sizeof(b), sizeof(c), strlen(c), sizeof(d), strlen(d), sizeof(g), sizeof(e), sizeof(f));

	return 0;
}
