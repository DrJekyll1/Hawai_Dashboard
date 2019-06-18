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
   * set header for HTTP-Resquest
   * @returns {{headers: HttpHeaders}}
   * */
 private header() {
   console.log(this.oAuthService.getAccessToken());
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
   * get files from FileServer
   */
  getFiles(): Observable<Files[]> {
    const body = '';
    console.log(this.fileServerUrl + 'file/list');
    return this.http.get<Files[]>(this.fileServerUrl + 'file/list', this.header())
      .pipe(
        catchError(this.handleError('getFiles', []))
      );
  }

  /**
   * download a file
   */
    downloadFile(tag: string): Observable<Files[]> {

      return this.http.get<Files[]>(this.fileServerUrl + 'file/download/' + tag, this.header())
        .pipe(
          catchError(this.handleError('downloadFiles', []))
        );
  }

  /**
   * upload a file
   */
  uploadFile(selectedFile: File) {
    const uploadData = new FormData();

    uploadData.append('tag', 'testclienttag');
    uploadData.append('File', selectedFile, selectedFile.name);

    this.http.post(this.fileServerUrl + 'file/add', uploadData, this.header());
  }

  /**
   * delete a file
   */
  deleteFile(tag: string): Observable<File[]> {

    const body = '';

    return this.http.post<File[]>(this.fileServerUrl + 'file/delete/' + tag, body, this.header())
      .pipe(
        catchError(this.handleError('deleteFiles', []))
      );
  }



}
