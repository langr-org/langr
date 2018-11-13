/**
 * @file js/popup.js
 * @brief 
 * 
 * Copyright (C) 2018 YCY
 * All rights reserved.
 * 
 * @package chrome-app
 * @author feifei 2018/07/12 19:07
 * 
 * $Id$
 */
chrome.storage.local.get('params', function (items) {
    // 对全局变量赋值
    if (typeof items.params != "undefined") {
        params = items.params
        document.getElementById('sn').value = params.sn
        document.getElementById('key').value = params.key
        document.getElementById('url').value = params.url
        document.getElementById('xid').value = params.id
        console.log('popup: ' + params.sn);
    }
});

let btn1 = document.getElementById('btn1');
let btn2 = document.getElementById('btn2');
let btn3 = document.getElementById('btn3');

btn1.onclick = function () {
    let obj = getAllvalue()
    chrome.storage.local.set({
        'params': obj
    }, function () {
        console.log('保存成功！');
    })
    // let color = document.getElementById('ycy').value;
    // console.log('popup.js: ycy:' + color);
    chrome.tabs.query({
        active: true,
        currentWindow: true
    }, function (tabs) {
        chrome.tabs.executeScript(
            tabs[0].id, {
                code: 'console.log("启动"+location.href);s0click();'
            });
        //{code: 'document.body.style.backgroundColor = "' + color + '";'});
        //{code: 'document.body.style.backgroundColor = "blue";'});
    });
    // 保存数据
}

btn2.onclick = function (element) {
    chrome.tabs.query({
        active: true,
        currentWindow: true
    }, function (tabs) {
        chrome.tabs.executeScript(
            tabs[0].id, {
                code: 'console.log("暂停");stopclick();'
            });
        //console.log('popup.js: url:' + tabs[0].url);
    });
}

btn3.onclick = function (element) {
    let pv = Number(document.getElementById('page').value)
    chrome.tabs.query({
        active: true,
        currentWindow: true
    }, function (tabs) {
        chrome.tabs.executeScript(
            tabs[0].id, {
                code: 'console.log("第' + pv + '页");s2click(' + pv + ');'
            });
    });
}

// 获取配置选项值
function getAllvalue() {
    let snvalue = document.getElementById('sn').value
    let keyvalue = document.getElementById('key').value;
    let urlvalue = document.getElementById('url').value;
    let xidvalue = Number(document.getElementById('xid').value)
    let obj = {
        sn: snvalue,
        key: keyvalue,
        url: urlvalue,
        id: xidvalue
    }
    return obj
}
