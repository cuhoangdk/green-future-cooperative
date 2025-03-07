export interface LoginCredentials {
  email: string
  password: string
}

export interface LoginResponse {
  token_type: string
  expires_in: number
  access_token: string
  refresh_token: string
}