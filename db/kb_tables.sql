-- Jalankan SQL ini di database helpdesk_v2

CREATE TABLE IF NOT EXISTS kb_categories (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(50) DEFAULT 'bi-folder',
    color VARCHAR(20) DEFAULT '#2563EB',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kb_articles (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    category_id INT UNSIGNED NOT NULL,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    excerpt TEXT,
    content LONGTEXT NOT NULL,
    tags VARCHAR(255),
    read_time VARCHAR(20),
    status ENUM('draft','published','archived') DEFAULT 'draft',
    use_for_ai TINYINT(1) DEFAULT 1,
    view_count INT UNSIGNED DEFAULT 0,
    embedding JSON,
    created_by INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES kb_categories(id) ON DELETE CASCADE,
    FULLTEXT KEY ft_search (title, excerpt, content)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
