export interface FileModel {
  id: number;
  fileNumber: number;
  department: String;
  state: Boolean;
}

export interface NewFile {
  fileNumber: number;
  yearNumber: number;
}
