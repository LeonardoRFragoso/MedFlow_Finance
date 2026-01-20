import { ref, onMounted, onUnmounted } from 'vue'
import websocketService from '@/services/websocket'

export function useWebSocket() {
  const isConnected = ref(false)
  const isConnecting = ref(false)
  const reconnectAttempts = ref(0)

  const updateState = () => {
    const state = websocketService.getState()
    isConnected.value = state.isConnected
    isConnecting.value = state.isConnecting
    reconnectAttempts.value = state.reconnectAttempts
  }

  const listeners = []

  onMounted(() => {
    updateState()

    // Listen to connection state changes
    const unsubConnected = websocketService.on('connected', () => {
      updateState()
    })

    const unsubDisconnected = websocketService.on('disconnected', () => {
      updateState()
    })

    listeners.push(unsubConnected, unsubDisconnected)
  })

  onUnmounted(() => {
    // Clean up listeners
    listeners.forEach(unsub => unsub())
  })

  const connect = (url, token) => {
    websocketService.connect(url, token)
    updateState()
  }

  const disconnect = () => {
    websocketService.disconnect()
    updateState()
  }

  const send = (type, payload) => {
    return websocketService.send(type, payload)
  }

  const on = (event, callback) => {
    return websocketService.on(event, callback)
  }

  const off = (event, callback) => {
    websocketService.off(event, callback)
  }

  return {
    isConnected,
    isConnecting,
    reconnectAttempts,
    connect,
    disconnect,
    send,
    on,
    off
  }
}
