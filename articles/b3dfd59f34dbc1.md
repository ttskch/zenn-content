---
title: "ツイートの前後のツイートを検索するブックマークレット"
emoji: "🎻"
type: "idea"
topics: ["twitter"]
published: true
---

ググってもなんかシンプルなソリューションが見当たらなかったのでブックマークレットを書きました。

```js
var m = location.href.match(/twitter\.com\/(.+)\/status\/(\d+)/);
var u = m[1];
var t = m[2];
var d = new Date(document.querySelector('time').getAttribute('datetime'));
var d2s = (d) => `${d.toLocaleString().replaceAll('/', '-').replace(' ', '_')}_JST`;
var sn = d2s(new Date(new Date(d).setHours(d.getHours() - 1)));
var un = d2s(new Date(new Date(d).setHours(d.getHours() + 1)));
var l = encodeURI(`https://twitter.com/search?q=from:${u} since:${sn} until:${un}&f=live`);
window.open(l, '_blank');
```

```
javascript:var%20m=location.href.match(/twitter\.com\/(.+)\/status\/(\d+)/);var%20u=m[1];var%20t=m[2];var%20d=new%20Date(document.querySelector('time').getAttribute('datetime'));var%20d2s=(d)=>`${d.toLocaleString().replaceAll('/','-').replace('%20','_')}_JST`;var%20sn=d2s(new%20Date(new%20Date(d).setHours(d.getHours()-1)));var%20un=d2s(new%20Date(new%20Date(d).setHours(d.getHours()+1)));var%20l=encodeURI(`https://twitter.com/search?q=from:${u} since:${sn} until:${un}&f=live`);window.open(l,'_blank');
```

![](https://tva1.sinaimg.cn/large/008vxvgGgy1h7a9msqnvqj30tk0uajsx.jpg)

このブックマークレットをツイートの詳細ページで実行すると、同じ投稿者による前後1時間のツイートの検索結果ページが別タブで開きます。

![](https://img.esa.io/uploads/production/attachments/15064/2022/10/19/77821/89973c7c-1386-477b-9da0-8fdcffc08c79.gif)

よろしければどうぞ✋
