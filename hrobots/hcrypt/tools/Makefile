all: hcrypt

hcrypt: crypt.c zencode.c
	gcc -o hcrypt crypt.c zencode.c -lz

clean:
	rm *.o *.so hcrypt
