/*!\file testaes.c
 * \brief Unit test program for the Blowfish cipher.
 * \author Bob Deblier <bob.deblier@telenet.be>
 * \ingroup UNIT_m
 */

#include <stdio.h>
#include "bcrypt.h"

int main(int argc, char * argv[])
{
	/*byte key[32];
	byte src[16] = {0};
	byte dst[16] = {0};*/

	int i, j;
	unsigned char result[8192] = {0};
	unsigned char buf[8192] = {0};
	unsigned char fn[64] = {0};

	if ( argc > 1 ) {
		//for ( i = 1; i < argc; i++ ) {
			i = 1;
			printf("(%s)aes(%s)%d\n", argv[i], argv[i] + 2, strlen(argv[i]) - 2);
			if ( argv[i][0] == '-' && argv[i][1] == 'e') {
				j = aes_encrypt(buf, argv[i] + 2, strlen(argv[i]) - 2, argv[i + 1]);
				printf("\naes_key=%s\n", argv[i + 1]);
				printf("aes_encrypt(%s)(j:%d)=", argv[i] + 2, j);
				printf("\ndump buf:");
				hex_dump(buf, j);

				j = aes_decrypt(result, buf, j, argv[i + 1]);
				printf("\naes_decrypt()(j:%d)=%s\n", j, result);
				hex_dump(result, strlen(result));

			} else if ( argv[i][0] == '-' && argv[i][1] == 'd') {
				j = aes_decrypt(buf, argv[i] + 2, strlen(argv[i]) - 2, argv[i + 1]);
				printf("aes_key=%s\n", argv[i + 1]);
				printf("aes_decrypt(%s)(j:%d)=", argv[i] + 2, j);
				printf("%s\n", buf);
				hex_dump(buf, j);

			} /*else if ( strcmp(argv[i], "-t") ) {
				;
			} */else if ( argv[i][0] == '-' && argv[i][1] == 'f' && argv[i][2] == 'e') {
				printf("aes_enfile()\n");
				aes_enfile(argv[i + 1], argv[i] + 3, argv[i + 2]);
				aes_file(strcat(strcpy(fn, argv[i] + 3), ".efile"), argv[i] + 3, argv[i + 2], ENCRYPT);
			} else if ( argv[i][0] == '-' && argv[i][1] == 'f' && argv[i][2] == 'd') {
				printf("aes_defile()\n");
				aes_defile(argv[i + 1], argv[i] + 3, argv[i + 2]);
				aes_file(strcat(strcpy(fn, argv[i] + 3), ".dfile"), argv[i] + 3, argv[i + 2], DECRYPT);
			}
		//}
	} else {
		printf("usage: \taes  [[-fd|-fe]filename] [-eencrypt] [-ddecrypt] key\n");
	}

	return 0;
}

