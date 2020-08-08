/**
 * Dagong
 *
 * @auth xxx <xxx@xxx.org> 2020/06/10 11:50
 * $Id$
 */

var host = "ws://" + document.domain + ":8765/?token=abcdef"
var ws = null

var user = $cookies.get('user') ? $cookies.get('user') : ''
var roomid = $cookies.get('roomid') ? $cookies.get('roomid') : 606

var login = new Vue({
    el: '#login',
    data: {
        user: user,
        roomid: roomid,
        loginShow: true,
        toastShow: false,
        toastText: ''
    },
    created: function () {
        //document.getElementById('zhezhao').style.display = ''
    },
    methods: {
        join_room: function() {
            if (this.user == null || this.user.length < 4) {
                this.toast('请输入有效用户名')
                return
            }
            if (isNaN(this.roomid) || this.roomid < 1) {
                this.toast('请输入有效房间号')
                return
            }
            //console.log('user:' + this.user + ' roomid:' + this.roomid)
            //console.log('cookie:' + this.$cookies.get('user'))
            $cookies.set('user', this.user, '30d')
            $cookies.set('roomid', this.roomid, '30d')
            //console.log('cookie' + $cookies.get('user'))
            this.loginShow = false
            user = this.user
            roomid = this.roomid
            document.getElementById('zhezhao').style.display = 'none'
            /* 连接ws,登陆 */
            ws = $.websocket({url: host, onMessage: doWsMessage, onOpen: doWsOpen})       /* connect ws */
        },
        toast: function(str) {
            var v = this
            v.toastText = str
            v.toastShow = true
            setTimeout(function(){
                v.toastShow = false
            }, 2000)
        }
    }
})

/* */
var pk_arr = ['0', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 
            'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 
            'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 
            'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 
            'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 
            'o', 'p', 'q', 'r', '<', '>'];
/* 发牌 */
var u1_pk = '>>ABCCFGJKLLRSWZachjlmmnnpq';
/* message.split('').reverse().join('') */
//var pks = [1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,52,53,54];

/* 服务器牌转换为图片编号 */
function poker2number(pks) {
    var len = pks.length
    var res = []
    for (i = 0; i < len; i++) {
        res.push(pk_arr.indexOf(pks[i]))
    }
    //console.log(pks + ' ' + len + 'res:' + res)
    return res;
}

/* 选中/取消 扑克 */
function select_poker(pk, is_select = true) {
    //console.log('current_pk:' + current_pk)
    if (is_select) {
        current_pk = current_pk + pk
    } else {
        current_pk = current_pk.replace(pk, '')
    }
    //console.log('current_pk ok:' + current_pk)
}

/* 手中的牌 */
var pks = poker2number(u1_pk)
/* 当前 选中的牌型 */
var current_pk = '';

var u1 = new Vue({
    el: '#u1',
    data: {
        pks: pks,
        pk_arr: pk_arr
    },
    methods: {
        selectPk: function(o, pk) {
            ob = document.getElementById('u1_k'+o)
            v = ob.style.getPropertyValue('top')
            if (v == undefined || v == '') {
                /* 选中 */
                ob.style.setProperty('top', '30px')
                console.log(pk + '选中')
                select_poker(pk)
            } else {
                /* 取消选中 */
                ob.style.removeProperty('top')
                console.log(pk + '取消选中')
                select_poker(pk, false)
            }
        },
        btn_pass: function() {
            console.log('u1 pass')
            //ws.send('play:0')
            ws.send(JSON.stringify({a: 'room', m: 'play', d: {a1: 0}}))
        },
        btn_play: function() {
            console.log('u1 play')
            console.log(current_pk)
            ws.send(JSON.stringify({a: 'room', m: 'play', d: {a1: current_pk}}))
        }
    }
})

/*
var u2 = new Vue({
    el: '#u2',
    data: {
        u2pk: 18
    },
    methods: {
        selectPk(v) {
            console.log(v)
        }
    }
})

var u3 = new Vue({
    el: '#u3',
    data: {
    },
    methods: {
        selectPk(v) {
            console.log(v)
        }
    }
})

var u4 = new Vue({
    el: '#u4',
    data: {
        u4pk: 24
    }
})
*/

/*
var u1_btn = new Vue({
    el: '#u1-btn',
    data: {
    },
    methods: {
        btn_pass: function() {
            console.log('u1 pass')
        },
        btn_play: function() {
            console.log('u1 play')
        }
    }
})
*/

var play_time_out = function () {
}

function send_msg(msg) {
    console.log('send:' + msg)
}

/*
document.getElementById('u1-pass').onclick = function () {
    console.log('u1-pass')
}
document.getElementById('u1-play').onclick = function () {
    console.log('pk:' + current_pk)
}
*/

/* do dagong api */

/**
 * dologin
 */
function doWsOpen(e) {
    console.log('doWsOpen:' + user + ':' + roomid + ':' + e)
    ws.send(JSON.stringify({a: 'login', m: 'token', d: {a1: user, a2: 'token-' + user}}))
    ws.send(JSON.stringify({a: 'room', m: 'add', d: {a1: roomid}}))
    ws.send(JSON.stringify({a: 'room', m: 'ready', d: {a1: 1}}))
}

/**
 * do
 */
function doWsMessage(e) {
    console.log('doWsMessage:' + e)
    var s = e.substr(0, 5)
    if (s == 'hello' || s == 'close') {
        console.log('doWs:' + e)
        return
    }
    var obj = JSON.parse(e)
    //var str = JSON.stringify(obj)
    code = Number(obj.c)
    /* */
    if (code >= 200 && code <= 300) {
        console.log('doWs Mothed:' + obj.m + ' code:' + code)
        if (obj.m == 'room-sendpoker') {
            /* 发牌 */
            console.log('doWs d:' + obj.d)
            u1_pk = obj.d.a1
            u1.pks = poker2number(u1_pk)
        } else if (obj.m == 'room-play') {
            /* 出牌 */
            if (user == obj.d.a1) {
            }
        } else if (obj.m == 'room-gameover') {
        }
    }
    if (code != 0 && code != 1) {
        console.log('doWs Error:' + code)
    } else {
        console.log('doWs Msg:' + obj.m)
    }
}
