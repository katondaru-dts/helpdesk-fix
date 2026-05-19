-- Jalankan query ini di database helpdesk_v2 melalui phpMyAdmin atau tool database lainnya

ALTER TABLE tickets ADD COLUMN requester_name VARCHAR(100) NULL AFTER reporter_id;
