export interface FileModel {
  id_exp: number;
  id_lista_despacho?: number;
  numero_exp: number;
  anio_exp: String;
  caratula: string;
  reservado?: number;
  dependencia: string;
  tipo_lista?: string;
  state?: Boolean;
}

export interface NewFile {
  fileNumber: number;
  yearNumber: number;
  dispatch: number;
}
