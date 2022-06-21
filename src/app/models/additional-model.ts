export interface Additional{
  id: Number;
  name: String;
  email: String;
}

export interface newAdditionalDTO extends Omit <Additional, 'id'>{}

export interface UpAdditionalDTO extends newAdditionalDTO{
  id?: Number;
}
