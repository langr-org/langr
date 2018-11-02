#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <sys/types.h>
#include <sys/stat.h>
#include <unistd.h>
#include "../zencode.h"

int main(int argc, char**argv)
{
	FILE	*fp;
	struct	stat	stat_buf;
	char	*datap, *newdatap;
	int	datalen, newdatalen;
	int	cryptkey_len = sizeof (lcrypt_key) / 2;
	char	oldfilename[256];
	int	i;

	if (argc != 2) {
		fprintf(stderr, "Usage: filename.\n");
		exit(0);
	}
	fp = fopen(argv[1], "r");
	if (fp == NULL) {
		fprintf(stderr, "File not found(%s)\n", argv[1]);
		exit(0);
	}

	fstat(fileno(fp), &stat_buf);
	datalen = stat_buf.st_size;
	datap = (char*)malloc(datalen + HY_CRYPT_LEN);
	fread(datap, datalen, 1, fp);
	fclose(fp);

	sprintf(oldfilename, "%s.old.php", argv[1]);

	if (memcmp(datap, HY_CRYPT, HY_CRYPT_LEN) == 0) {
		fprintf(stderr, "Already Crypted(%s)\n", argv[1]);
		exit(0);
	}

	fp = fopen(oldfilename, "w");
	if (fp == NULL) {
		fprintf(stderr, "Can not create backup file(%s)\n", oldfilename);
		exit(0);
	}
	fwrite(datap, datalen, 1, fp);
	fclose(fp);

	newdatap = zencode(datap, datalen, &newdatalen);

	for(i=0; i<newdatalen; i++) {
		newdatap[i] = (char) lcrypt_key[(newdatalen - i) % cryptkey_len] ^ (~(newdatap[i]));
	}

	fp = fopen(argv[1], "w");
	if (fp == NULL) {
		fprintf(stderr, "Can not create crypt file(%s)\n", oldfilename);
		exit(0);
	}
	fwrite(HY_CRYPT, HY_CRYPT_LEN, 1, fp);
	fwrite(newdatap, newdatalen, 1, fp);
	fclose(fp);
	fprintf(stderr, "Success Crypting(%s)\n", argv[1]);
	free(newdatap);
	free(datap);
}
