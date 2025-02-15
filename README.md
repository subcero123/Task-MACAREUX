## Installation

### Clone the repository:
```bash
git clone <repository-url>
cd project
```

### Set up the environment:
- Create a `.env` file with the necessary configurations.
```bash
cp .env.example .env
```

### Set up the database:
- Run the provided SQL script to set up the database.
```bash
mysql -u your_user -p your_database < sql_scrip.sql
```

## Running the Program

### Start the server:
```bash
php -S localhost:8000 -t public
```

### Access the application:
Open your browser and go to:  
[http://localhost:8000/index.html](http://localhost:8000/index.html)

---

## インストール

### リポジトリをクローンします:
```bash
git clone <repository-url>
cd project
```

### 環境を設定します:
```bash
cp .env.example .env
```

### データベースを設定します:
- 提供されたSQLスクリプトを実行してデータベースを設定します。
```bash
mysql -u your_user -p your_database < sql_scrip.sql
```

## プログラムの実行

### サーバーを起動します:
```bash
php -S localhost:8000 -t public
```

### アプリケーションにアクセス:
ブラウザで以下のURLを開きます:  
[http://localhost:8000/index.html](http://localhost:8000/index.html)