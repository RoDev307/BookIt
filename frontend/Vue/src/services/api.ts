
const API_URL = 'http://localhost:8000'

function getXsrfToken(): string {
  const cookie = document.cookie
    .split('; ')
    .find((row) => row.startsWith('XSRF-TOKEN='))

  return cookie
    ? decodeURIComponent(cookie.substring('XSRF-TOKEN='.length))
    : ''
}

async function request<T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  const headers = new Headers(options.headers)

  headers.set('Accept', 'application/json')
  headers.set('X-Requested-With', 'XMLHttpRequest')

  if (options.body && !(options.body instanceof FormData)) {
    headers.set('Content-Type', 'application/json')
  }

  const method = (options.method || 'GET').toUpperCase()

  if (!['GET', 'HEAD', 'OPTIONS'].includes(method)) {
    const token = getXsrfToken()

    if (token) {
      headers.set('X-XSRF-TOKEN', token)
    }
  }

  const response = await fetch(`${API_URL}${endpoint}`, {
    ...options,
    headers,
    credentials: 'include',
  })

  const data = await response.json().catch(() => ({}))

  if (!response.ok) {
    const message =
      data.message ||
      Object.values(data.errors || {}).flat().join(' ') ||
      `Error HTTP ${response.status}`

    throw new Error(String(message))
  }

  return data as T
}

export async function csrfCookie(): Promise<void> {
  const response = await fetch(`${API_URL}/sanctum/csrf-cookie`, {
    credentials: 'include',
    headers: {
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  })

  if (!response.ok) {
    throw new Error('No se pudo obtener la cookie CSRF.')
  }
}

export async function apiGet<T>(endpoint: string): Promise<T> {
  return request<T>(endpoint)
}

export async function apiPost<T>(
  endpoint: string,
  body: unknown,
): Promise<T> {
  return request<T>(endpoint, {
    method: 'POST',
    body: JSON.stringify(body),
  })
}

export async function apiPut<T>(
  endpoint: string,
  body: unknown,
): Promise<T> {
  return request<T>(endpoint, {
    method: 'PUT',
    body: JSON.stringify(body),
  })
}
