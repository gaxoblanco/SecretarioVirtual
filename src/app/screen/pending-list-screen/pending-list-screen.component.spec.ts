import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PendingListScreenComponent } from './pending-list-screen.component';

describe('PendingListScreenComponent', () => {
  let component: PendingListScreenComponent;
  let fixture: ComponentFixture<PendingListScreenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PendingListScreenComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(PendingListScreenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
