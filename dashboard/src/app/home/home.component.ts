import { Component, OnInit, ViewChild } from '@angular/core';
import { Subscription } from 'rxjs/Subscription';
import {AuthService} from '../services/auth.services';
import { Observable } from 'rxjs/Observable';
import { Client } from '../models/client';
import { Files } from '../models/files';
import {FileServerServices} from '../services/fileServer.services';
import {MatSort, MatTableDataSource, MatPaginator} from '@angular/material';

import {HttpClient, HttpParams, HttpHeaders, HttpResponse} from '@angular/common/http';
import { saveAs } from 'file-saver/FileSaver';


@Component({
  selector: 'app-home',
  templateUrl: './home.component.html',
  styleUrls: ['./home.component.css']
})
export class HomeComponent implements OnInit {


  clients: Client[];
  files: Files[];
  message: string;
  signedIn: Observable<boolean>;
  filesLoaded: Promise<boolean>;
  clientsLoaded: Promise<boolean>;
  selectedFile: File;


  /*
   declaration for the table
    */
  displayedColumns: string[] = ['no', 'name', 'extension', 'version', 'clientName', 'date'];
  dataSource = new MatTableDataSource<Files>(this.files);

  @ViewChild(MatPaginator) paginator: MatPaginator;
  @ViewChild(MatSort) sort: MatSort;
  headers: string[];

  constructor(private authService: AuthService,
              private fileServerService: FileServerServices,
              ) {

    this.signedIn = authService.isSignedIn();

  }

  ngOnInit() {
    this.getClients();
    this.getFiles();
    this.dataSource.sort = this.sort;
  }

  onFileChanged(event) {
    this.selectedFile = event.target.files[0];
  }

  /**
   * get Clients from File-Manager
   */
  getClients(): void {
    this.fileServerService.getClients()
      .subscribe(clients => {
        this.clients = clients,
          this.clientsLoaded = Promise.resolve(true);

      });
  }

  /**
   * get Files from File-Maanger
   */
  getFiles(): void {
    this.fileServerService.getFiles()
      .subscribe(files => {
        this.files = files,
          this.filesLoaded = Promise.resolve(true);
         this.dataSource.data = this.files;

      });
  }


}



