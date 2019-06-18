import {Injectable} from '@angular/core';
import {HttpClient, HttpParams, HttpHeaders} from '@angular/common/http';
import {OAuthService} from 'angular-oauth2-oidc';
import {Observable} from 'rxjs/Observable';

import { Client } from '../models/client';
import { Files } from '../models/files';
import { HttpErrorHandler, HandleError } from '../http-error-handler.services';
import {catchError} from 'rxjs/operators';


@Injectable ()
export class FileServerServices {


  /*
  URL from File-Manager
   */
  fileServerUrl = 'http://localhost:5001/';

  private handleError: HandleError;

  constructor(
    private http: HttpClient,
    private oAuthService: OAuthService,
    httpErrorHandler: HttpErrorHandler
  ) {
    this.handleError = httpErrorHandler.createHandleError('FileServerService');
  }


  /**
   * set the Header for the HTTP-Protocol
   * @returns {{headers: HttpHeaders}}
   */
  private header() {

    const httpOptions = {

     headers: new HttpHeaders({
       'Content-Type': 'application/json',
       'Accept': 'application/json',
       'Authorization': this.oAuthService.getAccessToken()
     })
   };
   return httpOptions;
  }

  /**
   * get Clients from FileServer
   */
  getClients (): Observable<Client[]> {
    return this.http.get<Client[]>(this.fileServerUrl + 'client/list', this.header())
      .pipe(
        catchError(this.handleError('getClients', []))
      );
  }

  /**
   * get Files from FileServer
   */
  getFiles(): Observable<Files[]> {
    return this.http.get<Files[]>(this.fileServerUrl + 'file/list', this.header())
      .pipe(
        catchError(this.handleError('getFiles', []))
      );
  }

}
