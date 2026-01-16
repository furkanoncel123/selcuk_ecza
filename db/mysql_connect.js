const mysql = require('mysql2');
require('dotenv').config();

const connection = mysql.createPool({
    user: process.env.DB_USERNAME, // Adapted from .env (DB_USERNAME vs DB_USER)
    password: process.env.DB_PASSWORD,
    host: process.env.DB_HOST,
    database: process.env.DB_DATABASE,
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    socketPath: process.env.DB_SOCKET // Support MAMP socket if needed
});

module.exports = connection.promise();
