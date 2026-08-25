<img src="logo/Voltova5-nobg.png">

# Project Voltiva Laravel Test Project
## Cara Deploy oi

### 1. Diclone dulu

```zsh
git clone https://github.com/bluebleaze/Project_Voltiva.git 
cd Project_Voltiva
```

### 2. Install depedencies & Vendor
```zsh
composer install
```

### 3. Ganti .env.exampe ke .env lalu configure
```zsh
cp .env.example .env
code .env
```
### 4. Artisan migrate untuk sinkronisasi Database 
```zsh
php artisan migrate
```
### 5. (Opsional) Tambahkan seeder untuk database dummy
```zsh
php artisan migrate --seed
```
### 6. Run projeknya
```zsh
php artisan serve
```




