import { AuthConfig} from 'angular-oauth2-oidc';

export const OAuthConfig: AuthConfig = {

  // URL to identity provider
  issuer: 'http://localhost:5000',

  // URL of the SPA to redirect the user to after login
  redirectUri: window.location.origin + '/home',

  // USe Open Id Connect during implicit flow
  oidc: true,

  // URL of the SPA to redirect the user after silent refresh
  silentRefreshRedirectUri: window.location.origin + '/home',

  // SPA Client Id
  clientId: 'testClient',


  // set the scope for the permission the client should request
  scope: 'openid profile email FileServer.full_access',

  // response type for the token
  responseType: 'id_token token',

  // URL of the SPA to redirect after logout
  postLogoutRedirectUri: 'http://localhost:4201',

  showDebugInformation: true,

  sessionChecksEnabled: true
}
