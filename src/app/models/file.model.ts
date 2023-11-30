export interface FileModel {
  id_exp: number;
  id_lista_despacho?: number;
  numero_exp: number;
  anio_exp: number;
  caratula: string;
  reservado?: number;
  dependencia: string;
  tipo_lista?: string;
  state?: boolean;
  id_user?: number;
}

export interface NewFile {
  fileNumber: number;
  yearNumber: number;
  dispatch: number;
}
