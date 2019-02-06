# PHP Serial Killer

## 本ツールについて
本ツールは、PHPのシリアライズ/デシリアライズの変換を行うツールです。

unserialize()を悪用したPHP Object Injectionを試行するためのいくつか便利機能も備えております。

## 使用手順
1. Docker runで実行します。

    > $ docker build . -t serial-killer
    > $ docker run -it -d --rm --name serial-killer -p 80:80 --privileged=true serial-killer

2. ブラウザで以下のURLにアクセスしてください。

    `http://localhost/index.php`


    他にも練習問題を3問ほど用意しています。是非試してみてください。

    各問題には `FLAG{****}` 形式の文字列が隠されています。これらを見つける事が問題のゴールです。

    `http://localhost/level1.php`

    `http://localhost/level2.php`

    `http://localhost/level3.php`


## 止めたい場合
> $ docker stop serial-killer

