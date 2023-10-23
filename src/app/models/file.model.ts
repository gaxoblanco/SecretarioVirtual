export interface FileModel {
  id_exp: number;
  numero_exp: number;
  anio_exp: String;
  caratula: string;
  dependencia: string;
  tipo_lista?: string;
  state?: Boolean;
}

export interface NewFile {
  fileNumber: number;
  yearNumber: number;
}
