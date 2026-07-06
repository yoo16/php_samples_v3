-- ============================================================
-- schema.sql - 社員データベース 初期設定
-- 実行例: mysql -u root -p < schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS employees_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE employees_db;

-- ---------- テーブル作成 ----------

CREATE TABLE IF NOT EXISTS companies (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  name         VARCHAR(100) NOT NULL,
  industry     VARCHAR(50)  NOT NULL,
  founded_year YEAR         NOT NULL,
  location     VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS departments (
  id         INT  AUTO_INCREMENT PRIMARY KEY,
  company_id INT  NOT NULL,
  name       VARCHAR(100) NOT NULL,
  budget     BIGINT       NOT NULL,
  FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS employees (
  id            INT  AUTO_INCREMENT PRIMARY KEY,
  company_id    INT  NOT NULL,
  department_id INT  NOT NULL,
  name          VARCHAR(100) NOT NULL,
  email         VARCHAR(150) NOT NULL UNIQUE,
  position      VARCHAR(50)  NOT NULL,
  salary        INT          NOT NULL,
  hired_at      DATE         NOT NULL,
  FOREIGN KEY (company_id)    REFERENCES companies(id)   ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE CASCADE
);

-- ---------- サンプルデータ ----------

INSERT INTO companies (name, industry, founded_year, location) VALUES
  ('株式会社テックワークス',     'IT',   2010, '東京都渋谷区'),
  ('山田製造株式会社',           '製造', 1985, '大阪府大阪市'),
  ('フューチャーバンク株式会社', '金融', 1998, '東京都千代田区');

INSERT INTO departments (company_id, name, budget) VALUES
  (1, '開発部',         50000000),
  (1, '営業部',         30000000),
  (1, 'デザイン部',     20000000),
  (2, '製造部',         80000000),
  (2, '品質管理部',     25000000),
  (3, 'リテール営業部', 60000000),
  (3, 'システム部',     40000000);

INSERT INTO employees (company_id, department_id, name, email, position, salary, hired_at) VALUES
  (1, 1, '田中 太郎',   'tanaka@techworks.co.jp',     'シニアエンジニア',   750000, '2015-04-01'),
  (1, 1, '鈴木 花子',   'suzuki@techworks.co.jp',     'エンジニア',         550000, '2019-07-15'),
  (1, 1, '佐藤 健一',   'sato@techworks.co.jp',       'エンジニア',         480000, '2021-04-01'),
  (1, 2, '山本 美咲',   'yamamoto@techworks.co.jp',   '営業マネージャー',   680000, '2016-09-01'),
  (1, 2, '中村 大介',   'nakamura@techworks.co.jp',   '営業担当',           420000, '2022-04-01'),
  (1, 3, '伊藤 愛',     'ito@techworks.co.jp',        'UIデザイナー',       530000, '2018-06-01'),
  (2, 4, '渡辺 正',     'watanabe@yamada-mfg.co.jp',  '製造部長',           820000, '2000-04-01'),
  (2, 4, '高橋 浩二',   'takahashi@yamada-mfg.co.jp', '製造スタッフ',       380000, '2020-04-01'),
  (2, 5, '松本 由美',   'matsumoto@yamada-mfg.co.jp', '品質管理主任',       590000, '2010-10-01'),
  (3, 6, '井上 誠',     'inoue@futurebank.co.jp',     '営業部長',           900000, '2005-04-01'),
  (3, 6, '木村 さくら', 'kimura@futurebank.co.jp',    '営業担当',           450000, '2023-04-01'),
  (3, 7, '林 俊介',     'hayashi@futurebank.co.jp',   'システムエンジニア', 700000, '2012-07-01');
