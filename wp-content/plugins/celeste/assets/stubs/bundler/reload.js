import events from 'events';
import express from 'express';
import { createServer } from 'http';
import { WebSocketServer, WebSocket } from 'ws';

const app = express();
const server = createServer(app);
const wss = new WebSocketServer({ server });

const emitter = new events.EventEmitter();

// Store all connected WebSocket clients
const clients = new Set();

const startServer = (port = 7354) => {
  wss.on('connection', (ws) => {
    console.log('✅ Client connection established');
    clients.add(ws); // Add the newly connected client to the set

    ws.on('close', () => {
      clients.delete(ws); // Remove the client from the set when it disconnects
    });

    ws.on('message', (message) => {
      console.log('received: %s', message);
    });
  });

  // Broadcast the reload event to all connected clients
  emitter.on('reload', (changedFile) => {
    console.log('🌀 Reloading all connected clients');
    for (const client of clients) {
      if (client.readyState === WebSocket.OPEN) {
        client.send(JSON.stringify({
          event: 'reload',
          changedFile
        }));
      }
    }
  });

  // Endpoint to receive GET or POST requests
  app.all('/trigger-reload', (req, res) => {
    console.log('🚀 Event Trigger endpoint hit');
    emitter.emit('reload', 'Endpoint Triggered'); // Emitting reload event
    res.send('Event triggered and broadcasted to WebSocket clients');
  });

  server.listen(port, () => {
    console.log(`🔗 Instant Reload Server listening on port ${port}`);
  });
}

export { emitter };
export default startServer;
