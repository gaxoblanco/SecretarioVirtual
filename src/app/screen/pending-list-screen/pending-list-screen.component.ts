import { Component, OnInit } from '@angular/core';
import { FilesService } from 'src/app/services/files.service';

@Component({
  selector: 'app-pending-list-screen',
  templateUrl: './pending-list-screen.component.html',
  styleUrls: ['./pending-list-screen.component.scss']
})
export class PendingListScreenComponent implements OnInit {

  constructor(private FileSer : FilesService,) {

  }

  ngOnInit(): void {
  }

}
