import { Component, OnInit, ViewChild } from '@angular/core';
import { Subscription } from 'rxjs/Subscription';
import { OAuthService } from 'angular-oauth2-oidc';
import {AuthService} from '../services/auth.services';
import { Observable } from 'rxjs/Observable';
import { Client } from '../models/client';
import { Files } from '../models/files';
import {FileServerServices} from '../services/fileServer.services';
// import {Sort} from '@angular/material';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';
import {HttpClient, HttpParams, HttpHeaders, HttpResponse} from '@angular/common/http';
import { saveAs } from 'file-saver/FileSaver';


@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {


  files: Files[];
  message: string;
  signedIn: Observable<boolean>;
  filesLoaded: Promise<boolean>;

  selectedFile: File;
  fileServerUrl = 'http://localhost:5001/';


  displayedColumns: string[] = ['no', 'name', 'extension', 'version', 'clientName', 'date'];
  // displayedColumns: string[] = ['name', 'extension', 'version', 'clientName', 'date'];
  dataSource = new MatTableDataSource<Files>(this.files);

  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort) sort: MatSort;


  userDataSubscription: Subscription;
  userData: boolean;
  isAuthorizedSubscription: Subscription;
  isAuthorized: boolean;

  headers: string[];

  constructor(private oauthService: OAuthService,
              private authService: AuthService,
              private fileServerService: FileServerServices,
              private http: HttpClient,
              private oAuthService: OAuthService) {

    this.signedIn = authService.isSignedIn();
    console.log('Wert von singedIN in Home: ', this.signedIn);

  }

  ngOnInit() {
    this.getFiles();
    this.dataSource.sort = this.sort;
  }

  onFileChanged(event) {
    this.selectedFile = event.target.files[0];
  }

  onUpload() {
    const uploadData = new FormData();
    const tag = 'testclienttag';
    uploadData.append('File', this.selectedFile, this.selectedFile.name);

    this.http.post(this.fileServerUrl + 'file/add/' + tag, uploadData, this.header())
      .subscribe(res => {
        console.log(res);
      });
  }

  /**
   * call to delete a file
   * @param {string} filename
   * @param {string} version
   * @param {string} date
   */
  delete(filename: string, version: string, date: string) {
    const tag = 'testclienttag';
    const data = {
      version: version,
      filename: filename,
      date: date
    };


    this.http.post(this.fileServerUrl + 'file/delete/' + tag, data, this.header())
      .subscribe(res => {
        console.log(res);
      });
  }

  /**
   * call to download a file
   * @param {string} value
   * @param {string} version
   * @param {string} date
   * @returns {Subscription}
   */
  download(value: string, version: string, date: string) {

    let contentType = '';
    const extension: string[] = value.split('.');

    if (extension[1] === 'txt') {
      contentType = 'text/plain';
    }
    if (extension[1] === 'html') {
      contentType = 'text/html';
    }
    if (extension[1] === 'json') {
      contentType = 'application/json';
    }
    if (extension[1] === 'xml') {
      contentType = 'application/xml';
    }

    const HTTPOptions = {
      headers: new HttpHeaders({
        'Accept': contentType,
        'Authorization': this.oAuthService.getAccessToken(),
      }),
      'responseType': 'blob' as 'json',
      'observe': 'response' as 'response'
    };

    return this.http.get(this.fileServerUrl + 'file/download/' + value + '/' + version + '/' + date, HTTPOptions)
      .subscribe(
        resp => { this.saveToFileSystem(resp, contentType);
          // display its headers
          const keys = resp.headers.keys();
          this.headers = keys.map(key =>
            `${key}: ${resp.headers.get(key)}`);
          console.log(this.headers);
        });
  }

  /**
   * save downloadedd file to system
   * @param response
   * @param contentType
   */
  private saveToFileSystem(response, contentType) {

    console.log(response.headers.get('Disposition'));
    const contentDispositionHeader: string = response.headers.get('Disposition');

    if (contentDispositionHeader != null) {
      const parts: string[] = contentDispositionHeader.split(';');

      const filename = parts[1].split('=')[1];
      filename.replace(/"/g, '');
      const newFileName = filename.replace(/"/g, '');
      console.log(filename);
      console.log(response.body);
      const blob = new Blob([response.body], { type: contentType });
      console.log(blob.type);
      saveAs(blob, newFileName);
    }

  }

  /**
   * set header for HTTP-Request
   * @returns {{headers: HttpHeaders}}
   */
  private header() {
    console.log(this.oAuthService.getAccessToken());
    const httpOptions = {

      headers: new HttpHeaders({
        'Accept': 'application/json',
        'Authorization': this.oAuthService.getAccessToken()
      })
    };
    return httpOptions;
  }

  /**
   * call to get a list of files
   */
  getFiles(): void {
    this.fileServerService.getFiles()
      .subscribe(files => {
        this.files = files,
          this.filesLoaded = Promise.resolve(true);
       // this.sortedData = this.files.slice();
         this.dataSource.data = this.files;
        // this.dataSource.sort = this.sort;
      });


  }

  get name() {
    const claims = this.oauthService.getIdentityClaims();
    if (!claims) {
      return null;
    }
    return claims['name'];
  }

  get email() {
    const claims = this.oauthService.getIdentityClaims();
    if (!claims) {
      return null;
    }
    return claims['email'];
  }

  get firstName() {
    const claims = this.oauthService.getIdentityClaims();
    if (!claims) {
      return null;
    }
    return claims['given_name'];
  }

  logout(): void {
    this.oauthService.logOut();
  }
}


