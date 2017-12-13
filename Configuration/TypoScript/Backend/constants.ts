module.tx_ats {
  view {
    # cat=module.tx_ats/file; type=string; label=Path to template root (BE)
    templateRootPath = EXT:ats/Resources/Private/Templates/
    # cat=module.tx_ats/file; type=string; label=Path to template partials (BE)
    partialRootPath = EXT:ats/Resources/Private/Partials/
    # cat=module.tx_ats/file; type=string; label=Path to template layouts (BE)
    layoutRootPath = EXT:ats/Resources/Private/Layouts/
  }
  persistence {
    # cat=module.tx_ats//a; type=string; label=Default storage PID
    storagePid =
  }
}
