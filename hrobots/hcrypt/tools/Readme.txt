加密工具最好在linux上编译和使用：

编译：
gcc -o hcrypt php_hcrypt.h zencode.c crypt.c
使用：
find . -name "*.php" | xargs -n1 hcrypt
