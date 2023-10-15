export interface Additional {
  id?: Number;
  firstName: String;
  Semail: String;
  secreataryId?: String;
  data?: any;
}

export interface newAdditionalDTO extends Omit<Additional, 'id'> {}

export interface UpAdditionalDTO extends newAdditionalDTO {
  id?: Number;
}
