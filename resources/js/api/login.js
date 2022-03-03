import http from '../libs/http'

export const login = ({ username, password,remeber, clientId, clientSecret, provider }) => {
  return http.post('/oauth/login', {
    username,
    password,
    remeber,
    provider,
    grant_type: 'password',
    client_id: clientId,
    client_secret: clientSecret
  })
}

export const logout = () => {
    return http.post('/oauth/logout', {
    })
}
