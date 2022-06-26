import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PendingFileComponenetComponent } from './pending-file-componenet.component';

describe('PendingFileComponenetComponent', () => {
  let component: PendingFileComponenetComponent;
  let fixture: ComponentFixture<PendingFileComponenetComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      declarations: [ PendingFileComponenetComponent ]
    })
    .compileComponents();
  });

  beforeEach(() => {
    fixture = TestBed.createComponent(PendingFileComponenetComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
