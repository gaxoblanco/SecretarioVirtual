export interface User {
  id?: string;
  firstName: string;
  lastName: string;
  DIN?: number;
  adders?: string;
  city?: string;
  email: string;
  password: string;
  Check?: boolean;
  subscribe: string;
}

export interface CreateUserDTO extends Omit<User, 'id'> {}

export interface LoginModel {
  email: string;
  password: string;
}
