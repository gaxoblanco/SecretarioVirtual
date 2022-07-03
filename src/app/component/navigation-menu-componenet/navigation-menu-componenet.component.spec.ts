import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NavigationMenuComponenetComponent } from './navigation-menu-componenet.component';

describe('NavigationMenuComponenetComponent', () => {
  let component: NavigationMenuComponenetComponent;
  let fixture: ComponentFixture<NavigationMenuComponenetComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ NavigationMenuComponenetComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(NavigationMenuComponenetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
