-- Migrasi: Tambah kolom deskripsi pada diseases & symptoms dan isi data awal
-- Jalankan file ini di phpMyAdmin (Import) atau MySQL CLI

CREATE DATABASE IF NOT EXISTS ispa_db;
USE ispa_db;

-- Tambah kolom description jika belum ada
ALTER TABLE diseases ADD COLUMN IF NOT EXISTS description TEXT NULL;
ALTER TABLE symptoms ADD COLUMN IF NOT EXISTS description TEXT NULL;

-- Isi deskripsi penyakit
UPDATE diseases SET description = 'Peradangan pada bronkus yang ditandai batuk, dahak kental, napas berbunyi (mengi), dan sesak; sering dipicu infeksi virus atau bakteri.' WHERE code = 'P001'; -- Bronkitis
UPDATE diseases SET description = 'Peradangan pada rongga sinus yang menimbulkan hidung tersumbat/pilek, nyeri/tekanan pada wajah, sakit kepala, dan penurunan penciuman.' WHERE code = 'P002'; -- Sinusitis
UPDATE diseases SET description = 'Infeksi saluran napas bawah (terutama pada anak) yang mengenai bronkiolus, menyebabkan sesak, napas cepat, dan mengi.' WHERE code = 'P003'; -- Bronkiolitis
UPDATE diseases SET description = 'Infeksi jaringan paru (alveoli) yang menyebabkan demam, batuk, sesak, nyeri dada, dan bisa disertai dahak kental atau bercampur darah.' WHERE code = 'P004'; -- Pneumonia
UPDATE diseases SET description = 'Peradangan faring (tenggorokan) yang ditandai nyeri menelan, rasa kering/terbakar, demam ringan, dan batuk.' WHERE code = 'P005'; -- Faringitis
UPDATE diseases SET description = 'Peradangan epiglotis yang dapat mengancam jalan napas; ditandai nyeri tenggorokan berat, demam, suara serak, drooling, dan sesak.' WHERE code = 'P006'; -- Epiglotitis
UPDATE diseases SET description = 'Peradangan selaput paru (pleura) yang menimbulkan nyeri dada tajam terutama saat bernapas dalam atau batuk, disertai sesak.' WHERE code = 'P007'; -- Pleuritis
UPDATE diseases SET description = 'Infeksi virus saluran napas atas (common cold) dengan pilek, bersin, tenggorokan tidak nyaman, demam ringan, dan gejala sistemik ringan.' WHERE code = 'P008'; -- Common Cold
UPDATE diseases SET description = 'Sindrom mirip influenza (ILI) dengan demam, batuk, nyeri otot/sendi, menggigil, lemas, dan gejala pernapasan atas.' WHERE code = 'P009'; -- ILI

-- Isi deskripsi gejala
UPDATE symptoms SET description = 'Kenaikan suhu tubuh (>37,5°C) sebagai respons terhadap infeksi atau peradangan.' WHERE code = 'G001'; -- Demam
UPDATE symptoms SET description = 'Refleks untuk membersihkan jalan napas dari lendir atau iritan; dapat bersifat kering atau berdahak.' WHERE code = 'G002'; -- Batuk-Batuk
UPDATE symptoms SET description = 'Pembengkakan mukosa dan peningkatan produksi sekret di hidung yang menyebabkan sumbatan/pilek.' WHERE code = 'G003'; -- Hidung Tersumbat/Pilek
UPDATE symptoms SET description = 'Rasa nyeri/tekanan di kepala; sering menyertai infeksi saluran napas atas atau sinusitis.' WHERE code = 'G004'; -- Sakit Kepala/Pusing
UPDATE symptoms SET description = 'Nyeri atau rasa terbakar di tenggorokan, terutama saat menelan; menandakan peradangan faring.' WHERE code = 'G005'; -- Sakit Tenggorokan
UPDATE symptoms SET description = 'Kesulitan menelan akibat nyeri atau obstruksi pada orofaring/laring.' WHERE code = 'G006'; -- Susah Menelan
UPDATE symptoms SET description = 'Penurunan energi/kelelahan umum akibat respon tubuh terhadap infeksi.' WHERE code = 'G007'; -- Badan Lemas & Lesu
UPDATE symptoms SET description = 'Kesulitan bernapas karena sumbatan/penyempitan saluran napas atau keterlibatan jaringan paru.' WHERE code = 'G008'; -- Sesak Nafas
UPDATE symptoms SET description = 'Refleks untuk mengeluarkan iritan dari rongga hidung; sering menyertai rinitis/common cold.' WHERE code = 'G009'; -- Bersin-Bersin
UPDATE symptoms SET description = 'Peningkatan jumlah napas per menit (takipnea), penanda distress pernapasan.' WHERE code = 'G010'; -- Frekuensi Nafas Cepat
UPDATE symptoms SET description = 'Suara napas tambahan (mengi/ronkhi) akibat lendir atau penyempitan saluran napas.' WHERE code = 'G011'; -- Suara Nafas Kasar
UPDATE symptoms SET description = 'Penurunan keinginan makan (inapetensia) yang sering terjadi saat sakit.' WHERE code = 'G012'; -- Nafsu Makan Berkurang
UPDATE symptoms SET description = 'Perubahan kualitas suara (parau/serak) akibat iritasi/infeksi pada laring/pita suara.' WHERE code = 'G013'; -- Suara Serak
UPDATE symptoms SET description = 'Perasaan tidak tenang/agitasi yang dapat muncul karena demam atau hipoksia.' WHERE code = 'G014'; -- Gelisah
UPDATE symptoms SET description = 'Gangguan memulai/menjaga tidur karena batuk, hidung tersumbat, atau ketidaknyamanan.' WHERE code = 'G015'; -- Susah Tidur
UPDATE symptoms SET description = 'Nyeri dada tajam/tusuk yang dapat memburuk saat batuk atau menarik napas dalam (pleuritik).' WHERE code = 'G016'; -- Nyeri Di Dada
UPDATE symptoms SET description = 'Penurunan kepekaan indra penciuman (hiposmia/anosmia) akibat inflamasi mukosa.' WHERE code = 'G017'; -- Berkurangnya Kemampuan Indra Penciuman
UPDATE symptoms SET description = 'Rasa nyeri/tekanan pada area wajah (sinus) khas pada sinusitis.' WHERE code = 'G018'; -- Wajah Terasa Nyeri/ Tertekan
UPDATE symptoms SET description = 'Bau napas tidak sedap (halitosis) yang dapat berhubungan dengan infeksi sinus/mulut.' WHERE code = 'G019'; -- Bau Mulut
UPDATE symptoms SET description = 'Nyeri gigi yang dapat menjalar pada sinusitis maksilaris.' WHERE code = 'G020'; -- Sakit Gigi
UPDATE symptoms SET description = 'Nyeri sendi/otot (arthralgia/mialgia) sebagai respon sistemik terhadap infeksi.' WHERE code = 'G021'; -- Nyeri Sendi/ Nyeri Otot
UPDATE symptoms SET description = 'Keringat berlebih disertai rasa menggigil akibat disregulasi suhu pada demam.' WHERE code = 'G022'; -- Berkeringat & Menggigil
UPDATE symptoms SET description = 'Produksi sputum kental kehijauan/kuning atau bercampur darah (hemoptisis) yang menandakan infeksi bakteri atau iritasi berat.' WHERE code = 'G023'; -- Batuk Dahak kental...
UPDATE symptoms SET description = 'Perubahan konsistensi dan frekuensi buang air besar; bisa menyertai infeksi sistemik atau efek obat.' WHERE code = 'G024'; -- Diare
UPDATE symptoms SET description = 'Rasa ingin muntah (mual) atau pengeluaran isi lambung (muntah), dapat dipicu demam/obat.' WHERE code = 'G025'; -- Mual/Muntah
UPDATE symptoms SET description = 'Nyeri pada bahu/punggung yang bisa terkait ketegangan otot pernapasan atau nyeri alih pleura.' WHERE code = 'G026'; -- Nyeri Bahu & Punggung
UPDATE symptoms SET description = 'Rembesan cairan bening dari hidung (rhinorea), khas common cold.' WHERE code = 'G027'; -- Hidung Berair
UPDATE symptoms SET description = 'Nyeri pada telinga yang dapat terkait gangguan Tuba Eustachius saat infeksi saluran napas atas.' WHERE code = 'G028'; -- Nyeri Telinga
UPDATE symptoms SET description = 'Peningkatan produksi air mata (lakrimasi), sering muncul pada iritasi/infeksi saluran napas atas.' WHERE code = 'G029'; -- Mata Berair
UPDATE symptoms SET description = 'Kekurangan cairan tubuh akibat demam, muntah, atau diare; ditandai mulut kering, haus, dan lemah.' WHERE code = 'G030'; -- Dehidrasi

-- Selesai
