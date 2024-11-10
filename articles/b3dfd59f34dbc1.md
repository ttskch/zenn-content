---
title: "ツイートの前後のツイート（RTを含む）を検索するブックマークレット"
emoji: "🎻"
type: "idea"
topics: ["twitter"]
published: true
---

**2024/11/10 追記**

ドメイン名が `twitter.com` のままだったため動かなくなっていました。`x.com` に書き換えたので今は動くようになっています。

---

ググってもなんかシンプルなソリューションが見当たらなかったのでブックマークレットを書きました。

```js
var m = location.href.match(/x\.com\/(.+)\/status/);
var u = m[1];
var d = new Date(document.querySelector('time').getAttribute('datetime'));
var d2s = (d) => `${d.toLocaleString().replaceAll('/', '-').replace(' ', '_')}_JST`;
var sn = d2s(new Date(new Date(d).setHours(d.getHours() - 1)));
var un = d2s(new Date(new Date(d).setHours(d.getHours() + 1)));
var l = encodeURI(`https://x.com/search?q=from:${u} include:nativeretweets since:${sn} until:${un}&f=live`);
window.open(l, '_blank');
```

```
javascript:var%20m=location.href.match(/x\.com\/(.+)\/status/);var%20u=m[1];var%20d=new%20Date(document.querySelector('time').getAttribute('datetime'));var%20d2s=(d)=>`${d.toLocaleString().replaceAll('/','-').replace('%20','_')}_JST`;var%20sn=d2s(new%20Date(new%20Date(d).setHours(d.getHours()-1)));var%20un=d2s(new%20Date(new%20Date(d).setHours(d.getHours()+1)));var%20l=encodeURI(`https://x.com/search?q=from:${u} include:nativeretweets since:${sn} until:${un}&f=live`);window.open(l,'_blank');
```

![](https://tva1.sinaimg.cn/large/008vxvgGgy1h7aan3pmrfj30ty0uodhe.jpg)

このブックマークレットをツイートの詳細ページで実行すると、同じ投稿者による前後1時間のツイート（RTを含む）の検索結果ページが別タブで開きます。

![](https://img.esa.io/uploads/production/attachments/15064/2022/10/19/77821/5be85b50-4761-48d2-b609-caaa99ebb090.gif)

よろしければどうぞ✋
