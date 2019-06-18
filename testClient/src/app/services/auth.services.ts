import {Injectable} from '@angular/core';
import { Router } from '@angular/router';
import {BehaviorSubject} from 'rxjs/BehaviorSubject';
import {HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs/Observable';
import {OAuthService} from 'angular-oauth2-oidc';

import { User } from '../models/user';

@Injectable() export class AuthService {

  public storage: Storage = sessionStorage;

  /**
   * Behavior subjects of the user´s status & data
   */
  private signinStatus = new BehaviorSubject<boolean>(false);
  private user = new BehaviorSubject<User>(new User());

  constructor(
    private http: HttpClient,
    private router: Router,
    private oAuthService: OAuthService
  ) {}

  public init(): void {
    // Tells the subscribers about the new status & data
    this.signinStatus.next(true);
    console.log('SignInStatus 1: ', this.signinStatus.getValue());
    this.user.next(this.getUser());
  }

  /**
   * logout the user
   */
  public signout(): void {
    // Because of using reference tokens as Access token. It can be revoke
    this.revokeToken();
    this.removeItem('discoveryDocument');
    this.removeItem('redirectUrl');

    // Tells the subscribers about the new status & data
    this.signinStatus.next(false);
    this.user.next(new User());

    this.oAuthService.logOut();
  }

  /**
   * refresh session and get a new reference token
   */
  public refreshSession(): void {
    this.revokeToken();
    this.removeItem('discoveryDocument');
    // Stores the attempted URL for redirecting.
    this.setItem('redirectUrl', this.router.url);

    // Tells all the subscribers about the new status & data.
    this.signinStatus.next(false);
    this.user.next(new User());

    this.oAuthService.initImplicitFlow();
  }

  /**
   * Get item from the session storage
   * @param {string} key
   * @returns {any}
   */
  public getItem(key: string): any {
    return JSON.parse(this.storage.getItem(key));
  }

  /**
   * save item in sessioin storage
   * @param {string} key
   * @param value
   */
  public setItem(key: string, value: any): void {
    this.storage.setItem(key, JSON.stringify(value));
  }

  /**
   * Delete the item from session storage
   * @param {string} key
   */
  public removeItem(key: string): void {
    this.storage.removeItem(key);
  }

  /**
   * Get the Header (Bearer) for the API call
   * @returns {HttpHeaders}
   */
 /* public getAuthorizationHeader(): HttpHeaders {
    // Creates header for the auth requests.
    let headers: HttpHeaders = new HttpHeaders().set('Content-Type', 'application/json');
    headers = headers.append('Accept', 'application/json');

    const token: string = this.oAuthService.getAccessToken();
    if (token !== '') {
      const tokenValue: string = 'Bearer ' + token;
      headers = headers.append('Authorization', tokenValue);
    }
    return headers;
  }*/


  /**
   * Is the user signed in
   * @returns {Observable<boolean>}
   */
  public isSignedIn(): Observable<boolean> {
    console.log('isSignedIn: ', this.signinStatus.getValue());
    return this.signinStatus.asObservable();

  }

  /**
   * was the user changed
   * @returns {Observable<User>}
   */
  public userChanged(): Observable<User> {
    return this.user.asObservable();
  }

  /**
   * Is the user in a group
   * @param {string} group
   * @returns {boolean}
   */
  public isInGroup(group: string): boolean {
    const user: User = this.getUser();
    const groups: string[] = user && typeof user.groups !== 'undefined' ? user.groups : [];
    return groups.indexOf(group) !== -1;
  }

  /**
   * Get the current user and his claims
   * @returns {User}
   */
  public getUser(): User {
    const user: User = new User();
    if (this.oAuthService.hasValidAccessToken()) {
      const userInfo: any = this.oAuthService.getIdentityClaims();

      user.givenName = userInfo.given_name || '';
      user.familyName = userInfo.family_name || '';
      user.email = userInfo.email;
      user.groups = userInfo.groups;
    }
    return user;
  }

  /**
   * revoke a token
   */
  public revokeToken(): void {
    const token: string = this.oAuthService.getAccessToken();

    if (token !== '') {
      const revocationEndpoint: string = this.getItem('discoveryDocument').revocation_endpoint;

      const headers: HttpHeaders = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');

      const params: any = {
        token: token,
        token_type_hint: 'access_token'
      };

      const body: string = this.encodeParams(params);

      this.http.post(revocationEndpoint, body, { headers: headers })
        .subscribe();
    }
  }

  /**
   * encode subject from the body
   * @param params
   * @returns {string}
   */
  private encodeParams(params: any): string {
    let body = '';
    for (const key of Object.keys(params)) {
      if (body.length) {
        body += '&';
      }
      body += key + '=';
      body += encodeURIComponent(params[key]);
    }
    return body;
  }

}
