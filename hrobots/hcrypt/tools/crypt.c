#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <sys/types.h>
#include <sys/stat.h>
#include "php_hcrypt.h"

int main(int argc, char**argv)
{
	FILE	*fp;
	struct	stat	stat_buf;
	char	*datap, *newdatap;
	int	datalen, newdatalen;
	int	cryptkey_len = sizeof(hh_cryptkey) / 2;
	char	oldfilename[256];
	int	i;

	if (argc != 2) {
		fprintf(stderr, "Usage: %s filename.\n", argv[0]);
		exit(0);
	}
	fp = fopen(argv[1], "r");
	if (fp == NULL) {
		fprintf(stderr, "File not found(%s)\n", argv[1]);
		exit(0);
	}

	fstat(fileno(fp), &stat_buf);
	datalen = stat_buf.st_size;
	datap = (char*) malloc(datalen + H2SCREW_LEN);
	fread(datap, datalen, 1, fp);
	fclose(fp);

	sprintf(oldfilename, "%s.h2", argv[1]);

	/* 比较检测文件是否已经有加密标示 */
	if (memcmp(datap, H2SCREW, H2SCREW_LEN) == 0) {
		fprintf(stderr, "Already Crypted(%s)\n", argv[1]);
		exit(0);
	}

	fp = fopen(oldfilename, "w");
	if (fp == NULL) {
		fprintf(stderr, "Can not create backup file(%s)\n", oldfilename);
		exit(0);
	}
	/* 备份源文件 */
	fwrite(datap, datalen, 1, fp);
	fclose(fp);

	/* 扯蛋 */
	newdatap = zencode(datap, datalen, &newdatalen);

	/* 喝喝 */
	for(i=0; i<newdatalen; i++) {
		newdatap[i] = (char) hh_cryptkey[(newdatalen - i) % cryptkey_len] ^ (~(newdatap[i]));
	}

	fp = fopen(argv[1], "w");
	if (fp == NULL) {
		fprintf(stderr, "Can not create crypt file(%s)\n", oldfilename);
		exit(0);
	}
	fwrite(H2SCREW, H2SCREW_LEN, 1, fp);
	fwrite(newdatap, newdatalen, 1, fp);
	fclose(fp);
	fprintf(stderr, "Success Crypting(%s)\n", argv[1]);
	free(datap);

	return 0;
}
