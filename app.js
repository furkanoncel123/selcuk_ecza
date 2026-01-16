const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const cors = require('cors');
require('dotenv').config();

const app = express();
const port = process.env.PORT || 3001;
const router = require("./routers");

app.use(cors());
app.use(express.static(path.join(__dirname, 'public')));
app.use(express.json());
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));

// Route all /api requests to routers/index.js
app.use("/api", router);

app.listen(port, () => {
    console.log(`Sunucu port ${port} üzerinde çalışıyor...`);
});
