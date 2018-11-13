/**
 * @file js/content-script.js
 * @brief 
 * 
 * Copyright (C) 2018 YCY
 * All rights reserved.
 * 
 * @package chrome-app
 * @author feifei 2018/07/12 19:23
 * 
 * $Id$
 */


// 日期参数格式化
Date.prototype.Format = function (fmt) {
    const year = fmt.getFullYear()
    const month = (fmt.getMonth() + 1) < 10 ? ('0' + (fmt.getMonth() + 1)) : fmt.getMonth() + 1
    const date = fmt.getDate() < 10 ? ("0" + fmt.getDate()) : fmt.getDate()
    return year + '/' + month + "/" + date
}
Date.prototype.Format2 = function (fmt) {
    fmt.setTime(fmt.getTime() - 90*24 * 60 * 60 * 1000);
    const year = fmt.getFullYear()
    //const month = (fmt.getMonth() - 2) < 10 ? ('0' + (fmt.getMonth() - 2)) : fmt.getMonth() - 2
    const month = (fmt.getMonth() + 1) < 10 ? ('0' + (fmt.getMonth() + 1)) : fmt.getMonth() + 1
    const date = fmt.getDate() < 10 ? ("0" + fmt.getDate()) : fmt.getDate()
    return year + '/' + month + "/" + date
}
// 页面加载获取日期
let time1 = new Date().Format(new Date())
let time2 = new Date().Format2(new Date());
// 保存ssclick函数的变量,可直接修改
let foo;
// 配置项
let sn, key, url, id = 16
// 银行接口url
let bankurl = 'https://ebsnew.boc.cn/BII/PsnGetUserProfile.do?_locale=zh_CN'
let bankurl_login = 'https://ebsnew.boc.cn/boc15/login.html'
let bankurl_page = 'https://ebsnew.boc.cn/boc15/welcome_ele.html'
// bank接口请求参数
//let commonParams = {"local":"zh_CN","agent":"WEB15","bfw-ctrl":"json","version":"","device":"Google,Chrome,68.0.3440.106","platform":"Apple,Mac OS X,10_13_0","plugins":"","page":"","ext":"","mac":"","serial":""}
let commonParams = {"local": "zh_CN","agent": "WEB15","bfw-ctrl": "json","version": "","device": "Google,Chrome,70.0.3528.4","platform": "Microsoft,Windows,10","plugins": "","page": "","ext": "","mac": "","serial": ""}
// bank接口请求头
let commonHeader = {
    Accept: "*/*",
    // Referer: "https://ebsnew.boc.cn/boc15/welcome_ele.html?v=20180823082329456&locale=zh&login=card&segment=1",
    'bfw-ctrl': 'json',
    // Origin: 'https: //ebsnew.boc.cn',
    'X-id': id,
    'X-Requested-With': 'XMLHttpRequest'
}
// bank信息
let userdata, accountSeq, conversationId = ''
// 点击开始按钮,调用s0click函数,获取配置项值,调用startclick函数,获取按钮,模拟点击,调用s1click,第一次请求银行接口,获取datas.response[0].result,佐维第二次请求接口的参数,调用s2click,获取银行数据,调用back,传回给后台

if (window.location.href.substr(0,38) == bankurl_login) {
    //alert('请登陆：' + window.location.href.substr(0,38))
    console.log('请登陆：' + window.location.href.substr(0,38))
    chrome.storage.local.get('params', function (items) {
        // 对全局变量赋值
        params = items.params
        let card_num = params.sn
        let pwd = params.key
        setTimeout(() => {
            console.log('登陆：' + card_num + pwd)
            //document.getElementById('txt_username_79443').value = card_num
            //document.getElementById('input_div_password_79445').value = pwd
            //$('#btn_login_79676').click()
        }, 3000)
    })
} else if (window.location.href.substr(0,44) == bankurl_page) {
    chrome.storage.local.get('params', function (items) {
        if (items && typeof items.params != "undefined" && typeof items.params.sn != "undefined" && typeof items.params.url != "undefined") {
            console.log('自动启动：' + items.params.sn + ' ' + items.params.url)
            s0click()
        }
    })
}

function s0click() {
    // 使用浏览器自带api获取传过来的参数
    chrome.storage.local.get('params', function (items) {
        // 对全局变量赋值
        params = items.params
        sn = params.sn
        key = params.key
        url = params.url
        id = params.id
        console.log('s0click: start b ' + sn + ' ' + url);
        getdata().then(res => {
            //console.log('s0click1:getdata');
            userdata = res.response[0].result
            accountSeq = userdata.accountSeq.toString()
            foo = startclick
            foo()
        })
    });
}

function getdata() {
    //console.log('getdata:');
    return new Promise((resolve, reject) => {
        let params = {
            header: commonParams,
            request: [{
                'id': id,
                'method': "PsnAccBocnetQryLoginInfo",
                'conversationId': null,
                'params': null
            }]
        }
        id++
        commonHeader['X-id'] = id;
        $.ajax(bankurl, {
            method: 'POST',
            //  数据类型必须为application/x-www-form-urlencoded之外的类型
            contentType: 'text/json',
            headers: commonHeader,
            //  数据必须转换为字符串
            data: JSON.stringify(params),
            success: function (datas) {
                resolve(datas)
            }
        })
    })
}

function startclick() {
    //console.log('startclick:');
    $('#div_transaction_details_740993').click()
    window.timer1 = setTimeout(() => {
        s1click()
    }, 3000);
}

function s1click() {
    //console.log('s1click:');
    let params = {
        header: commonParams,
        request: [{
            'id': id,
            'method': "PsnAccBocnetCreateConversation",
            'conversationId': null,
            'params': null
        }]
    }
    id++
    commonHeader['X-id'] = id;
    $.ajax(bankurl, {
        method: 'POST',
        //  数据类型必须为application/x-www-form-urlencoded之外的类型
        contentType: 'text/json',
        headers: commonHeader,
        //  数据必须转换为字符串
        data: JSON.stringify(params),
        success: function (datas) {
            id++
            if (!datas.response[0].result && datas.response[0].error) {
                console.log('logout:' + datas.response[0].error.message)
                window.location.href = bankurl_login
                //stopclick();
                return ;
            }
            conversationId = datas.response[0].result
            s2click(0)
        }
    })
}

function s2click(page) {
    //console.log('s2click:');
    window.timer2 = setTimeout(() => {
        let params = {
            header: commonParams,
            request: [{
                'conversationId': conversationId,
                'id': id,
                'method': "PsnAccBocnetQryDebitTransDetail",
                'params': {
                    accountSeq: accountSeq,
                    cashRemit: "",
                    currency: "",
                    currentIndex: page * 10,
                    endDate: time1,
                    pageSize: "10",
                    startDate: time2,
                    _refresh: page ? "false" : "true",
                }
            }]
        }
        id++
        commonHeader['X-id'] = id;
        $.ajax(bankurl, {
            method: 'POST',
            contentType: 'text/json',
            headers: commonHeader,
            //  数据必须转换为字符串
            data: JSON.stringify(params),
            success: function (datas) {
                let result = datas.response[0].result
                if (result != null && result.recordNumber > 10) {
                    console.log('s2click recordNumber:' + result.recordNumber)
                }
                back(result ? result.List : null)
            }
        })
    }, 1000);
}

function back(list) {
    //console.log('back:');
    // list为获取的列表数据
    /*let card_num = ''
    if (typeof document.getElementById('sel_pleaseselectaccount_740896').innerText != "undefined") {
        card_num = document.getElementById('sel_pleaseselectaccount_740896').innerText;
    }*/
    let card_num = userdata.accountNumber
    // 把数据传给后台
    let obj = {
        sn: sn,
        list: list,
        card_num: card_num,
        bank: 'boc'
    }
    let jsonobj = new Base64().encode(JSON.stringify(obj))
    let chksum = md5(JSON.stringify(obj) + key)
    let params = {
        res: jsonobj,
        chksum: chksum,
    }
    $.ajax(url, {
        method: 'POST',
        // contentType: 'text/json',
        headers: {
            AuthGC: sn+';'
        },
        data: params,
        success: function (res) {
            console.log('back:done ' + id);
        }
    })
    // 连续请求
    window.timer3 = setTimeout(() => {
        id++
        s1click(id, time1, time2)
    }, 120000);
}

// 暂停请求
function stopclick() {
    clearTimeout(timer1)
    clearTimeout(timer2)
    clearTimeout(timer3)
    foo = null
    console.log('stop:done ' + id);
}
