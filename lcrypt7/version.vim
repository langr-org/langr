"# $Id$
"vim $1 -e -s < version.vim
"vim $1 -S version.vim	/* windows xp */
"#%s/^\(#define\t[A-Z]*\t*\)\([0-9]\{2,4\}\)$/\=submatch(1).(submatch(2)+1)/g
"%s/\(define\t*VERSION_STRING\t*\"[a-z0-9]\{1,4\}\.[0-9]\{1,3\}\.\)\([0-9]\{1,5\}\) (\([0-9]\{1,5\}\))\(\"\)$/\=submatch(1).(submatch(2)+1)." (".(submatch(3)+1).")".submatch(4)/g
"%s/\(define\t*LAST_COMPILE_TIME\t*\"\)\(20[0-9][0-9][0-9 /\-:]\{15\}\)\(\"\)$/\=submatch(1).strftime("%Y-%m-%d %H:%M:%S").submatch(3)/g

"%s/\(define *VERSION_STRING\t*\"[a-z0-9]\{1,4\}\.[0-9]\{1,3\}\.\)\([0-9]\{1,5\}\)\(\"\)$/\=submatch(1).(submatch(2)+1).submatch(3)/g
%s/\(define.*LAST_COMPILE_TIME[ \t]*\"\)\(20.*\)\(\"\)/\=submatch(1).strftime("%Y-%m-%d %H:%M:%S").submatch(3)/g
%s/\(define.*_VERSION[ \t]*\"[a-z0-9]\{1,3\}\.[0-9]\{1,2\}\.\)\([0-9]\{1,4\}\) (\([0-9]\{1,4\}\))\(\"\)/\=submatch(1).(submatch(2)+1)." (".(submatch(3)+1).")".submatch(4)/g
w!
q
