const express = require('express');
const http = require('http');
const socketIo = require('socket.io');
const cors = require('cors');

const app = express();

// Habilitar CORS correctamente
app.use(cors({
    origin: "*",
    methods: ["GET", "POST"],
    allowedHeaders: ["Content-Type"]
}));

const server = http.createServer(app);
const io = socketIo(server, {
    cors: {
        origin: "*",
        methods: ["GET", "POST"]
    }
});

// Servir la página home.php cuando se accede a la raíz del servidor
app.get('/', (req, res) => {
    res.send(`<script>window.location.href = 'http://192.168.100.100/Home_ChestSystem/';</script>`);
});

// Evento cuando un cliente se conecta
io.on('connection', (socket) => {
    console.log('Nuevo cliente conectado:', socket.id);

    socket.on('disconnect', () => {
        console.log('Cliente desconectado:', socket.id);
    });
});

// Ruta para forzar la actualización de cualquier tabla
app.get('/refresh/:tabla', (req, res) => {
    const { tabla } = req.params;
    
    if (!tabla) {
        return res.status(400).json({ error: 'Debe proporcionar el nombre de la tabla' });
    }

    console.log(`🔄 Evento refresh_${tabla} emitido`);
    io.emit(`refresh_${tabla}`); // Emitir evento con el nombre de la tabla
    
    res.json({ message: `Actualización enviada para la tabla: ${tabla}` });
});

const PORT = 3001;
server.listen(PORT, '0.0.0.0', () => { 
    console.log(`🚀 Servidor corriendo en: http://192.168.100.100:${PORT}`);
});
