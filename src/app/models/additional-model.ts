export interface Additional {
  id?: number;
  firstName: string;
  Semail: string;
  secreataryId?: string;
  data?: any;
}

export interface newAdditionalDTO extends Omit<Additional, 'id'> {}

export interface UpAdditionalDTO extends newAdditionalDTO {
  id?: number;
}
