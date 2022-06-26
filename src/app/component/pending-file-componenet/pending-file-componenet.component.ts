import { Component, Input, OnInit } from '@angular/core';

@Component({
  selector: 'app-pending-file-componenet',
  templateUrl: './pending-file-componenet.component.html',
  styleUrls: ['./pending-file-componenet.component.scss']
})
export class PendingFileComponenetComponent implements OnInit {

  @Input() filesList: any = {
    id: '',
    fileNumber: '',
    department: '',
    state: Boolean,
  };

  constructor() { }

  ngOnInit(): void {
  }

}
