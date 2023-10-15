export interface Additional {
  id: Number;
  firstName: String;
  Semail: String;
  secreataryId?: String;
}

export interface newAdditionalDTO extends Omit<Additional, 'id'> {}

export interface UpAdditionalDTO extends newAdditionalDTO {
  id?: Number;
}
