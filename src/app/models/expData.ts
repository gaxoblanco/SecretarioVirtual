export interface ExpData {
  anio_exp: string;
  numero_exp: string;
  dependencia: string;
  moves: Move[];
  id_exp: string;
  caratula: string;
  reservado: string;
  tipo_lista: string;
}

export interface Move {
  despacho: string;
  estado: string;
  fecha_movimiento: string;
  id_exp: number;
  id_move: number;
  texto: string;
  titulo: string;
}
