" hua的vimrc文件
"
set nu
set ruler
"设置文件编码的检测顺序 (顺序比较重要)
"set fileencoding=utf-8
set fileencodings=ucs-bom,utf-8,gb18030,cp936,big5,euc-jp,euc-kr,latin1
set foldmethod=marker				"源文件按标签折行
set autoindent					"在换行时自动对齐上一行
set showcmd					"在右下角显示命令
set incsearch					"即时搜索
set hlsearch					"查找结果高亮度显示

"syn on
"set guifont=Monospace\ 11			"设置用于GUI图形用户界面的字体列表
"set mouse=a
"set autochdir					"自动切换当前目录为当前文件所在的目录 
" tab宽度
set tabstop=4
set expandtab					"tab替换为空格
set cindent shiftwidth=4
set autoindent shiftwidth=4
winpos 250 120					"设置gvim启动时的位置
"set lines=25 columns=108			"gvim启动时的窗口大小
set lines=37 columns=140			"gvim启动时的窗口大小
colorscheme pablo				"gvim配色方案

"== ctags设置===========================================
"==映射快捷键用于创建tags 文件==========================
map <C-F12>	:!ctags -R --c++-kinds=+p --fields=+iaS --extra=+q .<CR>
map <C-F1>	:!ctags -R --java-kinds=+p --fields=+iaS --extra=+q .<CR>
" 增强检索功能
"set tags+=$VIMRUNTIME\..\cpp.tags
set tags+=./tags,./../tags,./../../tags,./../../../tags,./../../../../tags
"==进行Tlist的设置===========================================
map <F11> :silent! Tlist<CR>
let Tlist_Ctags_Cmd='ctags'		"因为我们放在环境变量里，所以可以直接执行
let Tlist_Use_Right_Window=1		"让窗口显示在右边，0的话就是显示在左边
let Tlist_Show_One_File=0		"让taglist可以同时展示多个文件的函数列表，如果想只有1个，设置为1
let Tlist_File_Fold_Auto_Close=1	"非当前文件，函数列表折叠隐藏
let Tlist_Exit_OnlyWindow=1		"当taglist是最后一个分割窗口时，自动推出vim
let Tlist_Process_File_Always=1		"是否一直处理tags.1:处理;0:不处理
let Tlist_WinHeight=100			"设置窗口高度
let Tlist_WinWidth=24			"设置窗口宽度
let Tlist_Inc_Winwidth=0
let tlist_php_settings='php;c:class;f:function'			"不显示 variable
"let tlist_cpp_settings='c++;c:class;s:struct;f:function'	"不显示 variable
"let tlist_c_settings='c;s:struct;f:function'			"不显示 variable
"==配置omni全能补全====================================
let OmniCpp_MayCompleteScope = 1
let OmniCpp_DefaultNamespaces = ["std"]
set completeopt=longest,menu "关闭自动补全时的预览窗口

"set cpt=.,w,b,u,t,i,k				"自动补全搜索路径和顺序
"set complete+=k; set dictionary+=path		"字典补全
". <C-X><C-NP>当前缓冲区
"w 其它窗口的缓冲区; b 其它载入的缓冲区; u 卸载的缓冲区
"t <C-X><C-]>tags; i <C-X><C-I>include; k <C-X><C-K>字典
"<C-X><C-L>整行补全, <C-X><C-F>文件名补全
set cpt=.,w,b,u,i
let g:neocomplcache_enable_at_startup = 1	"NeoComplCache 自动补全插件
"ConqueTermSplit <command> 打开命令行窗口插件

"map E		<Esc>:e!~/.vimrc<CR>
"map D		<Esc>:r!date<CR><Esc>
map <F2>	<Esc><S-$>a	/*  */<Esc>	"注释
"map W		<Esc>:cd /usr/src/work/langr<CR><Esc>

if (has("win32") || has("win64") || has("win32unix"))
	let g:__ds = "\\"
else
	let g:__ds = "/"
endif

"进入编辑文件当前目录, 
function CdPwd()
	let current_path = strpart(expand("%"), 0, strridx(expand("%"), g:__ds))
	"let current_path = strpart(expand("%"), 0, strridx(expand("%"), "\\"))
	exec "cd ".current_path
	echo "cd: ".current_path
endfunction
map ~	<Esc>:call CdPwd()<CR>

" 设定文件浏览器目录为当前目录
"set bsdir=buffer
"set autochdir

"modify time
function ModifyTime()
	call append(line("."), " * @modify by Langr <hua@langr.org> ".strftime("%Y/%m/%d %H:%M"))
endfunction
function ModifyTime2()
	call append(line("."), " * @modify by Langr <langr@126.com> ".strftime("%Y/%m/%d %H:%M"))
endfunction
map <F7> <Esc>i<Esc>:call ModifyTime()<CR>
"map <F7> <Esc>i<Esc>:call ModifyTime2()<CR>

"打印文件头
function HeadGPL()
	call append(line("."), "/** ")
	call append(line(".")+1, " * @file ".expand("%"))
	call append(line(".")+2, " * @brief ")
	call append(line(".")+3, " * ")
	call append(line(".")+4, " * Copyright (C) ".strftime("%Y")." LangR.Org")
	call append(line(".")+5, " * ")
	call append(line(".")+6, " * This is free software; you can redistribute it and/or modify")
	call append(line(".")+7, " * it under the terms of the GNU General Public License as published by")
	call append(line(".")+8, " * the Free Software Foundation; either version 2, or (at your option)")
	call append(line(".")+9, " * any later version.")
	call append(line(".")+10, " * ")
	call append(line(".")+11, " * @package ".strpart(getcwd(), strridx(getcwd(), g:__ds) + 1))
	call append(line(".")+12, " * @author Langr <hua@langr.org> ".strftime("%Y/%m/%d %H:%M"))
	call append(line(".")+13, " * ")
	call append(line(".")+14, " * $Id$")
	call append(line(".")+15, " */")
endfunction

function HeadOTHER()
	call append(line("."), "/** ")
	call append(line(".")+1, " * @file ".expand("%"))
	call append(line(".")+2, " * @brief ")
	call append(line(".")+3, " * ")
	call append(line(".")+4, " * Copyright (C) ".strftime("%Y")." LangR.Org")
	call append(line(".")+5, " * ")
	call append(line(".")+6, " * Licensed under The MIT License")
	call append(line(".")+7, " * Redistributions of files must retain the above copyright notice.")
	call append(line(".")+8, " * ")
	call append(line(".")+9, " * @package ".strpart(getcwd(), strridx(getcwd(), g:__ds) + 1))
	call append(line(".")+10, " * @author Langr <hua@langr.org> ".strftime("%Y/%m/%d %H:%M"))
	call append(line(".")+11, " * @license MIT License (http://www.opensource.org/licenses/mit-license.php)")
	call append(line(".")+12, " * ")
	call append(line(".")+13, " * $Id$")
	call append(line(".")+14, " */")
endfunction
"map <F3> <Esc>:lang time en_GB.UTF-8<CR>:0<Esc>O<Esc>:call HeadGPL()<CR><Esc>:1d<CR>
map <F6> <Esc>:0<Esc>O<Esc>:call HeadGPL()<CR><Esc>:1d<CR>
map <F5> <Esc>:0<Esc>O<Esc>:call HeadOTHER()<CR><Esc>:1d<CR>

function HeadCopyright()
	call append(line("."), "/**")
	call append(line(".")+1, " * @file ".expand("%"))
	call append(line(".")+2, " * @brief ")
	call append(line(".")+3, " * ")
	call append(line(".")+4, " * Copyright (C) ".strftime("%Y")." Langr.Org")
	call append(line(".")+5, " * All rights reserved.")
	call append(line(".")+6, " * ")
	call append(line(".")+7, " * @package ".strpart(getcwd(), strridx(getcwd(), g:__ds) + 1))
	call append(line(".")+8, " * @author Langr <hua@langr.org> ".strftime("%Y/%m/%d %H:%M"))
	call append(line(".")+9, " * ")
	call append(line(".")+10, " * $Id$")
	call append(line(".")+11, " */")
endfunction
"map <F4> <Esc>:lang time en_GB.UTF-8<CR>:0<Esc>O<Esc>:call HeadCopyright()<CR><Esc>:1d<CR> 
map <F4> <Esc>:0<Esc>O<Esc>:call HeadCopyright()<CR><Esc>:1d<CR>

function FunInfo()
	call append(line("."), "/**")
	call append(line(".")+1, " * @fn")
	call append(line(".")+2, " * @brief ")
	call append(line(".")+3, " * @param ")
	call append(line(".")+4, " * @return ")
	call append(line(".")+5, " */")
	call append(line(".")+6, "function(args) /* {{{ */")
	call append(line(".")+7, "{")
	call append(line(".")+8, "	return ;")
	call append(line(".")+9, "} /* }}} */")
endfunction
map <F3> <Esc>O<Esc>:call FunInfo()<CR>

