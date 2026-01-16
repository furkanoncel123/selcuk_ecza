const db = require("../db/mysql_connect");
const bcrypt = require("bcrypt");

const kullanici_login = async (req, res) => {
    try {
        const { kullanici_adi, sifre } = req.body;
        const [existingUser] = await db.query("SELECT * FROM musteriler WHERE kullanici_adi=?", [kullanici_adi]);

        if (existingUser.length === 0) {
            return res.status(401).json({ error: "Kullanıcı adı veya şifre hatalı" });
        }

        const user = existingUser[0];
        const match = await bcrypt.compare(sifre, user.sifre);

        if (match) {
            return res.status(200).json({ message: "Giriş başarılı", user: { id: user.id, adi: user.adi, soyadi: user.soyadi } });
        } else {
            return res.status(401).json({ error: "Kullanıcı adı veya şifre hatalı" });
        }
    } catch (err) {
        console.log(err);
        return res.status(500).json({ error: "Sunucu hatası" });
    }
};

const kullanici_ekle = async (req, res) => {
    try {
        const { adi, soyadi, hesap_no, kullanici_adi, sifre } = req.body;
        const hashedPassword = await bcrypt.hash(sifre, 10);

        const [existingUser] = await db.query("SELECT * FROM musteriler WHERE kullanici_adi=?", [kullanici_adi]);
        if (existingUser.length > 0) {
            return res.status(400).json({ error: "Bu kullanıcı adı zaten alınmış" });
        }

        const [insertResult] = await db.query(
            "INSERT INTO musteriler (adi, soyadi, hesap_no, kullanici_adi, sifre) VALUES (?, ?, ?, ?, ?)",
            [adi, soyadi, hesap_no, kullanici_adi, hashedPassword]
        );
        return res.status(201).json({ message: "Kullanıcı başarıyla eklendi", kullanici_id: insertResult.insertId });
    } catch (err) {
        console.log(err);
        return res.status(500).json({ error: "Sunucu hatası" });
    }
};

const kullanici_getir = async (req, res) => {
    try {
        const [rows] = await db.query("SELECT id, adi, soyadi, hesap_no, kullanici_adi FROM musteriler");
        return res.status(200).json(rows);
    } catch (err) {
        console.log(err);
        return res.status(500).json({ error: "Sunucu hatası" });
    }
};

const satis_getir = async (req, res) => {
    try {
        // Adapted to match project schema: satis_verileri table
        // Querying last 100 records or all, adapting columns
        const [rows] = await db.query(`
            SELECT 
                s.tarih as sales_date, 
                s.miktar as sales_amount,
                e.isim as pharmacy_name,
                i.isim as drug_name
            FROM satis_verileri s
            JOIN eczaneler e ON s.eczane_id = e.id
            JOIN ilaclar i ON s.ilac_id = i.id
            LIMIT 100
        `);
        return res.status(200).json(rows);
    } catch (err) {
        console.log(err);
        return res.status(500).json({ error: "Sunucu hatası" });
    }
};

module.exports = { kullanici_login, kullanici_ekle, kullanici_getir, satis_getir };
