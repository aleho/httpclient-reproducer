# Curl warning and segfault reproduce

The expected log output is `PHP Warning:  PHP Request Shutdown: Cannot call the CURLOPT_PROGRESSFUNCTION in Unknown on line 0`.

Sometimes also segfaults:
```
Jul 18 10:15:27 shlep kernel: php[52470]: segfault at e0 ip 00007fdb9742b9b7 sp 00007fff062f3c18 error 6 in libc.so.6[7fdb972ff000+155000] likely on CPU 14 (core 7, socket 0)
Jul 18 10:15:27 shlep kernel: Code: 7e 6f 44 16 e0 48 29 fe 48 83 e1 e0 48 01 ce 0f 1f 40 00 c5 fe 6f 4e 60 c5 fe 6f 56 40 c5 fe 6f 5e 20 c5 fe 6f 26 48 83 c6 80 <c5> fd 7f 49 60 c5 fd 7f 51 40 c5 fd 7f 59 20 c5 fd 7f 21 48 83 c1
```

Reliably triggering the error is hard. PHP settings seem to help (but not in the Docker container?).

```
opcache.jit=disable
opcache.enable=0
opcache.enable_cli=0
```


## Local PHP

- php bin/console app:demo -vvv https://symfony.localhost/index.html


## Container PHP

Less reliable way of triggering, but sometimes (un)commenting code in
`FileDownloader` seems to help.

- docker compose up
- docker compose exec app bin/console app:demo -vvv https://symfony.test/index.html
