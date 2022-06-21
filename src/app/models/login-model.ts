export interface User{
    id: string;
    name: string;
    surname: string;
    DIN: number;
    adders: string;
    city: string;
    InputEmail1: string;
    InputPassword: string;
    Check: boolean;
  }

  export interface CreateUserDTO extends Omit<User, 'id'>{}

  export interface LoginModel{
    InputEmail1: string;
    InputPassword: string;
  }
