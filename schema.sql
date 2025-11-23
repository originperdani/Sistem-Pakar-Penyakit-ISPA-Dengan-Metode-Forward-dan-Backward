-- Schema untuk Sistem Pakar Diagnosa ISPA
CREATE DATABASE IF NOT EXISTS ispa_db;
USE ispa_db;

-- Tabel penyakit
CREATE TABLE IF NOT EXISTS diseases (
  code VARCHAR(10) PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

-- Tabel gejala
CREATE TABLE IF NOT EXISTS symptoms (
  code VARCHAR(10) PRIMARY KEY,
  name VARCHAR(200) NOT NULL
);

-- Tabel rules (kaidah)
CREATE TABLE IF NOT EXISTS rules (
  code VARCHAR(10) PRIMARY KEY,
  disease_code VARCHAR(10) NOT NULL,
  FOREIGN KEY (disease_code) REFERENCES diseases(code)
);

-- Relasi gejala per rule
CREATE TABLE IF NOT EXISTS rule_symptoms (
  rule_code VARCHAR(10) NOT NULL,
  symptom_code VARCHAR(10) NOT NULL,
  PRIMARY KEY (rule_code, symptom_code),
  FOREIGN KEY (rule_code) REFERENCES rules(code),
  FOREIGN KEY (symptom_code) REFERENCES symptoms(code)
);

-- Seed penyakit
INSERT INTO diseases (code, name) VALUES
('P001','Bronkitis'),('P002','Sinusitis'),('P003','Bronkiolitis'),('P004','Pneumonia'),('P005','Faringitis'),
('P006','Epiglotitis'),('P007','Pleuritis'),('P008','Common Cold'),('P009','ILI (Influenza Like Illness)');

-- Seed gejala (G001..G030)
INSERT INTO symptoms (code, name) VALUES
('G001','Demam'),('G002','Batuk-Batuk'),('G003','Hidung Tersumbat/Pilek'),('G004','Sakit Kepala/Pusing'),('G005','Sakit Tenggorokan'),
('G006','Susah Menelan'),('G007','Badan Lemas & Lesu'),('G008','Sesak Nafas'),('G009','Bersin-Bersin'),('G010','Frekuensi Nafas Cepat'),
('G011','Suara Nafas Kasar'),('G012','Nafsu Makan Berkurang'),('G013','Suara Serak'),('G014','Gelisah'),('G015','Susah Tidur'),
('G016','Nyeri Di Dada'),('G017','Berkurangnya Kemampuan Indra Penciuman'),('G018','Wajah Terasa Nyeri/ Tertekan'),('G019','Bau Mulut'),
('G020','Sakit Gigi'),('G021','Nyeri Sendi/ Nyeri Otot'),('G022','Berkeringat & Menggigil'),('G023','Batuk Dahak kental hijau/kuning/atau darah'),
('G024','Diare'),('G025','Mual/Muntah'),('G026','Nyeri Bahu & Punggung'),('G027','Hidung Berair'),('G028','Nyeri Telinga'),('G029','Mata Berair'),('G030','Dehidrasi');

-- Seed rules
INSERT INTO rules (code, disease_code) VALUES
('R1','P001'),('R2','P002'),('R3','P003'),('R4','P004'),('R5','P005'),('R6','P006'),('R7','P007'),('R8','P008'),('R9','P009');

-- R1: G001 AND G002 AND G003 AND G007 AND G008 AND G011 AND G023 AND G027 => P001
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R1','G001'),('R1','G002'),('R1','G003'),('R1','G007'),('R1','G008'),('R1','G011'),('R1','G023'),('R1','G027');

-- R2: G001,G002,G003,G004,G008,G009,G010,G013,G015,G017,G018,G019,G020 => P002
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R2','G001'),('R2','G002'),('R2','G003'),('R2','G004'),('R2','G008'),('R2','G009'),('R2','G010'),('R2','G013'),('R2','G015'),('R2','G017'),('R2','G018'),('R2','G019'),('R2','G020');

-- R3: G001,G002,G007,G008,G010,G011,G012,G015,G030 => P003
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R3','G001'),('R3','G002'),('R3','G007'),('R3','G008'),('R3','G010'),('R3','G011'),('R3','G012'),('R3','G015'),('R3','G030');

-- R4: G001,G002,G004,G008,G010,G012,G016,G023,G024,G025 => P004
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R4','G001'),('R4','G002'),('R4','G004'),('R4','G008'),('R4','G010'),('R4','G012'),('R4','G016'),('R4','G023'),('R4','G024'),('R4','G025');

-- R5: G001,G002,G004,G005,G006,G007,G009,G012,G013,G025 => P005
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R5','G001'),('R5','G002'),('R5','G004'),('R5','G005'),('R5','G006'),('R5','G007'),('R5','G009'),('R5','G012'),('R5','G013'),('R5','G025');

-- R6: G001,G004,G005,G006,G011,G013,G014 => P006
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R6','G001'),('R6','G004'),('R6','G005'),('R6','G006'),('R6','G011'),('R6','G013'),('R6','G014');

-- R7: G001,G002,G008,G010,G016,G021,G022,G023,G026 => P007
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R7','G001'),('R7','G002'),('R7','G008'),('R7','G010'),('R7','G016'),('R7','G021'),('R7','G022'),('R7','G023'),('R7','G026');

-- R8: G001,G002,G003,G004,G009,G013,G017,G027,G029 => P008
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R8','G001'),('R8','G002'),('R8','G003'),('R8','G004'),('R8','G009'),('R8','G013'),('R8','G017'),('R8','G027'),('R8','G029');

-- R9: G001,G002,G003,G004,G005,G006,G007,G008,G009,G010,G011,G012,G013,G017,G022,G029 => P009
INSERT INTO rule_symptoms (rule_code, symptom_code) VALUES
('R9','G001'),('R9','G002'),('R9','G003'),('R9','G004'),('R9','G005'),('R9','G006'),('R9','G007'),('R9','G008'),('R9','G009'),('R9','G010'),('R9','G011'),('R9','G012'),('R9','G013'),('R9','G017'),('R9','G022'),('R9','G029');